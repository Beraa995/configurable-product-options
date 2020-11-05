<?php
/**
 * @category  BKozlic
 * @package   BKozlic\ConfigurableOptions
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\ConfigurableOptions\Plugin\Block\Product\View\Type;

use BKozlic\ConfigurableOptions\Helper\Data;
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable as MagentoConfigurable;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Catalog\Model\ProductRepository;

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
     * Configurable constructor.
     * @param Json $jsonSerializer
     * @param Data $helper
     * @param ProductRepository $productRepository
     */
    public function __construct(
        Json $jsonSerializer,
        Data $helper,
        ProductRepository $productRepository
    ) {
        $this->jsonSerializer = $jsonSerializer;
        $this->helper = $helper;
        $this->productRepository = $productRepository;
    }

    /**
     * Add additional data for configurable product
     * @param MagentoConfigurable $subject
     * @param $result
     * @return string
     */
    public function afterGetJsonConfig(MagentoConfigurable $subject, $result)
    {
        if (!$this->helper->isModuleEnabled()) {
            return $result;
        }

        $configData = $this->jsonSerializer->unserialize($result);
        $currentProduct = $subject->getProduct();

        $configData['preselectEnabled'] = $this->helper->isPreselectEnabled();
        $configData['attributesUpdateEnabled'] = $this->helper->isSimpleProductUpdateEnabled();
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
    protected function getSimpleProductId($product)
    {
        return $product->getData('simple_product_preselect');
    }

    /**
     * Return simple product attribute values
     *
     * @param MagentoConfigurable $subject
     * @return array
     */
    protected function getSimpleProductUpdates($subject)
    {
        $content = [];
        $data = $this->jsonSerializer->unserialize($this->helper->getSimpleAttributes());

        foreach ($subject->getAllowProducts() as $product) {
            try {
                $childProduct = $this->productRepository->getById($product->getId());
            } catch (NoSuchEntityException $e) {
                continue;
            }

            $content[$product->getId()]['length'][] = count($data);

            foreach ($data as $value) {
                $content[$product->getId()]['identity'][] = $value['identity'];
                //@TODO Get value with EAV class
                $content[$product->getId()]['value'][] = $childProduct->getData($value['simple_product_attribute']) ?: null;
            }
        }

        return $content;
    }
}
