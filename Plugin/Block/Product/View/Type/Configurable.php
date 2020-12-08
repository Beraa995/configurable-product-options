<?php
/**
 * @category  BKozlic
 * @package   BKozlic\ConfigurableOptions
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\ConfigurableOptions\Plugin\Block\Product\View\Type;

use BKozlic\ConfigurableOptions\Helper\Data;
use BKozlic\ConfigurableOptions\Model\ModifierInterface;
use BKozlic\ConfigurableOptions\Model\ValueModifierPool;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable as MagentoConfigurable;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;

class Configurable
{
    /**
     * @var Json
     */
    protected $jsonSerializer;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var ProductResource
     */
    protected $productResource;

    /**
     * @var ValueModifierPool
     */
    protected $modifierPool;

    /**
     * Configurable constructor.
     * @param Json $jsonSerializer
     * @param Data $helper
     * @param ProductRepository $productRepository
     * @param ProductResource $productResource
     * @param ValueModifierPool $modifierPool
     */
    public function __construct(
        Json $jsonSerializer,
        Data $helper,
        ProductRepository $productRepository,
        ProductResource $productResource,
        ValueModifierPool $modifierPool
    ) {
        $this->jsonSerializer = $jsonSerializer;
        $this->helper = $helper;
        $this->productRepository = $productRepository;
        $this->productResource = $productResource;
        $this->modifierPool = $modifierPool;
    }

    /**
     * Add additional data for configurable product
     * @param MagentoConfigurable $subject
     * @param string $result
     * @return string
     */
    public function afterGetJsonConfig(MagentoConfigurable $subject, string $result)
    {
        if (!$this->helper->isModuleEnabled()) {
            return $result;
        }

        $configData = $this->jsonSerializer->unserialize($result);
        $currentProduct = $subject->getProduct();

        $configData['preselectEnabled'] = (bool)$this->helper->isPreselectEnabled();
        $configData['attributesUpdateEnabled'] = (bool)$this->helper->isSimpleProductUpdateEnabled();
        $configData['gallerySwitchStrategy'] = $this->helper->getGallerySwitchStrategy() ?: 'replace';
        $configData['simpleProduct'] = $this->getSimpleProductId($currentProduct);
        $configData['attributesForUpdate'] = $this->getSimpleProductUpdates($subject);

        return $this->jsonSerializer->serialize($configData);
    }

    /**
     * Return simple product id for preselect
     *
     * @param Product $product
     * @return string|null
     */
    protected function getSimpleProductId(Product $product)
    {
        return $product->getData('simple_product_preselect');
    }

    /**
     * Return simple product attribute values
     *
     * @param MagentoConfigurable $subject
     * @return array
     */
    protected function getSimpleProductUpdates(MagentoConfigurable $subject)
    {
        $content = [];

        if (!$this->helper->isSimpleProductUpdateEnabled()) {
            return $content;
        }

        $data = $this->jsonSerializer->unserialize($this->helper->getSimpleAttributes());

        foreach ($subject->getAllowProducts() as $product) {
            try {
                $childProduct = $this->productRepository->getById($product->getId());
            } catch (NoSuchEntityException $e) {
                continue;
            }

            foreach ($data as $value) {
                try {
                    $attribute = $attributeValue = $this->productResource->getAttribute(
                        $value['simple_product_attribute']
                    );
                } catch (LocalizedException $e) {
                    $attribute = null;
                }

                if (!$attribute) {
                    continue;
                }

                $attributeValue = $attribute->getFrontend()->getValue($childProduct);
                if (!trim($attributeValue)) {
                    continue;
                }

                $processedValue = $this->processAttributeValue(
                    $value['simple_product_attribute'],
                    $childProduct,
                    $value['selector'],
                    $attributeValue
                );
                $content[$product->getId()][] = [
                    'selector' => $value['selector'],
                    'value' => $processedValue,
                ];
            }
        }

        return $content;
    }

    /**
     * Process value with custom modifiers
     * @param string $attributeCode
     * @param ProductInterface $product
     * @param string $cssSelector
     * @param mixed $value
     * @return mixed
     */
    protected function processAttributeValue(string $attributeCode, ProductInterface $product, string $cssSelector, $value)
    {
        foreach ($this->modifierPool->getModifiers() as $modifier) {
            if ($modifier instanceof ModifierInterface) {
                $value = $modifier->processValue($attributeCode, $product, $cssSelector, $value);
            }
        }

        return $value;
    }
}
