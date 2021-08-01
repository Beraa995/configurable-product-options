<?php
/**
 * @category  BKozlic
 * @package   BKozlic\ConfigurableOptions
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
declare(strict_types=1);
namespace BKozlic\ConfigurableOptions\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;

/**
 * Class PreselectModifier
 */
class PreselectModifier extends AbstractModifier
{
    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * PreselectModifier constructor.
     * @param LocatorInterface $locator
     */
    public function __construct(
        LocatorInterface $locator
    ) {
        $this->locator = $locator;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        if ($this->getProduct()->getTypeId() === 'configurable') {
            $meta['simple_fieldset'] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Simple Product Preselect'),
                            'componentType' => Fieldset::NAME,
                            'dataScope' => 'data.product',
                            'collapsible' => true,
                            'sortOrder' => 5,
                        ],
                    ],
                ],
                'children' => [
                    'simple_product_preselect' => $this->getSimpleProductField()
                ],
            ];
        }

        return $meta;
    }

    /**
     * Get current product
     *
     * @return ProductInterface
     */
    public function getProduct()
    {
        return $this->locator->getProduct();
    }

    /**
     * Get simple products from current configurable product
     *
     * @return Product[]
     */
    public function getSimpleProducts()
    {
        return $this->getProduct()->getTypeInstance()->getUsedProducts($this->getProduct(), null);
    }

    /**
     * Get backend select field
     *
     * @return array
     */
    public function getSimpleProductField()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Simple Product'),
                        'componentType' => Field::NAME,
                        'formElement' => Select::NAME,
                        'dataType' => Text::NAME,
                        'sortOrder' => 10,
                        'options' => $this->getSimpleProductOptions(),
                    ],
                ],
            ],
        ];
    }

    /**
     * Get options for select
     *
     * @return array
     */
    public function getSimpleProductOptions()
    {
        $simpleProducts = [];
        $selectedName = null;
        $configurableOption = $this->getProduct()->getData('simple_product_preselect');
        $disabled = ['value' => 0, 'label' => 'Disabled'];

        foreach ($this->getSimpleProducts() as $index => $product) {
            if ($product->isSaleable()) {
                if ($configurableOption !== $product->getData('entity_id')) {
                    $simpleProducts[$index]['value'] = $product->getData('entity_id');
                    $simpleProducts[$index]['label'] = $product->getData('name') . ' - ' . $product->getData('sku');
                } else {
                    $selectedName = $product->getData('name') . ' - ' . $product->getData('sku');
                }
            }
        }

        if ($configurableOption && $selectedName) {
            $selected = ['value' => 0, 'label' => $selectedName];
            array_unshift($simpleProducts, $selected, $disabled);
        } else {
            array_unshift($simpleProducts, $disabled);
        }

        return $simpleProducts;
    }
}
