<?php
/**
 * @category  BKozlic
 * @package   BKozlic\ConfigurableOptions
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
declare(strict_types=1);
namespace BKozlic\ConfigurableOptions\Plugin\Block\Product\View\Type;

use BKozlic\ConfigurableOptions\Helper\Data;
use BKozlic\ConfigurableOptions\Service\GetProductAttributeValuesService;
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable as MagentoConfigurable;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class Configurable
 */
class Configurable
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var GetProductAttributeValuesService
     */
    protected $getProductAttributeValues;

    /**
     * Configurable constructor.
     * @param SerializerInterface $serializer
     * @param Data $helper
     * @param GetProductAttributeValuesService $getProductAttributeValues
     */
    public function __construct(
        SerializerInterface $serializer,
        Data $helper,
        GetProductAttributeValuesService $getProductAttributeValues
    ) {
        $this->serializer = $serializer;
        $this->helper = $helper;
        $this->getProductAttributeValues = $getProductAttributeValues;
    }

    /**
     * Add additional data for configurable product
     *
     * @param MagentoConfigurable $subject
     * @param string $result
     *
     * @return string
     */
    public function afterGetJsonConfig(MagentoConfigurable $subject, string $result)
    {
        if (!$this->helper->isModuleEnabled()) {
            return $result;
        }

        $configData = $this->serializer->unserialize($result);
        $currentProduct = $subject->getProduct();

        $configData['preselectEnabled'] = (bool)$this->helper->isPreselectEnabled();
        $configData['attributesUpdateEnabled'] = (bool)$this->helper->isSimpleProductUpdateEnabled();
        $configData['gallerySwitchStrategy'] = $this->helper->getGallerySwitchStrategy() ?: 'replace';
        $configData['simpleProduct'] = $this->getSimpleProductId($currentProduct);
        $configData['attributesForUpdate'] = $this->getSimpleProductUpdates($subject);

        return $this->serializer->serialize($configData);
    }

    /**
     * Return simple product id for preselect
     *
     * @param Product $product
     *
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
     *
     * @return array
     */
    protected function getSimpleProductUpdates(MagentoConfigurable $subject)
    {
        $content = [];

        if (!$this->helper->isSimpleProductUpdateEnabled()) {
            return $content;
        }

        foreach ($subject->getAllowProducts() as $product) {
            $productId = $product->getId();
            $content[$productId] = $this->getProductAttributeValues->execute($productId);
        }

        return $content;
    }
}
