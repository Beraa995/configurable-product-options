<?php
/**
 * @category  BKozlic
 * @package   BKozlic\ConfigurableOptions
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace BKozlic\ConfigurableOptions\Service;

use BKozlic\ConfigurableOptions\Helper\Data;
use BKozlic\ConfigurableOptions\Model\ModifierInterface;
use BKozlic\ConfigurableOptions\Model\ValueModifierPool;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class GetProductAttributeValuesService
 */
class GetProductAttributeValuesService
{
    /**
     * @var ValueModifierPool
     */
    protected $modifierPool;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var ProductResource
     */
    protected $productResource;

    /**
     * SimpleProductValueModifierService constructor.
     * @param SerializerInterface $serializer
     * @param ValueModifierPool $modifierPool
     * @param Data $helper
     * @param ProductRepository $productRepository
     * @param ProductResource $productResource
     */
    public function __construct(
        SerializerInterface $serializer,
        ValueModifierPool $modifierPool,
        Data $helper,
        ProductRepository $productRepository,
        ProductResource $productResource
    ) {
        $this->modifierPool = $modifierPool;
        $this->helper = $helper;
        $this->productRepository = $productRepository;
        $this->serializer = $serializer;
        $this->productResource = $productResource;
    }

    /**
     * Return attribute values and elements identifiers defined in the configuration
     *
     * @param int $productId
     * @param bool $asyncLoad
     *
     * @return array
     */
    public function execute($productId, bool $asyncLoad = false)
    {
        $content = [];
        try {
            $childProduct = $this->productRepository->getById($productId);
        } catch (NoSuchEntityException $e) {
            return $content;
        }

        $data = $this->serializer->unserialize($this->helper->getSimpleAttributes());
        foreach ($data as $value) {
            if (isset($value['load_asynchronously']) && (bool)$value['load_asynchronously'] !== $asyncLoad) {
                continue;
            }

            try {
                $attribute = $this->productResource->getAttribute(
                    $value['simple_product_attribute']
                );
            } catch (LocalizedException $e) {
                $attribute = null;
            }

            if (!$attribute) {
                $attributeValue = null;
            } else {
                $attributeValue = $attribute->getFrontend()->getValue($childProduct);
            }

            $processedValue = $this->modifyValue(
                $value['simple_product_attribute'],
                $childProduct,
                $value['selector'],
                $attributeValue
            );

            $content[] = [
                'selector' => $value['selector'],
                'value' => $processedValue,
            ];
        }

        return $content;
    }

    /**
     * Modify attribute value with defined modifiers
     *
     * @param string $attributeCode
     * @param ProductInterface $product
     * @param string $cssSelector
     * @param $value
     *
     * @return mixed
     */
    protected function modifyValue(string $attributeCode, ProductInterface $product, string $cssSelector, $value)
    {
        foreach ($this->modifierPool->getModifiers() as $modifier) {
            if ($modifier instanceof ModifierInterface) {
                $value = $modifier->processValue($attributeCode, $product, $cssSelector, $value);
            }
        }

        return $value;
    }
}
