<?php
/**
 * @category  BKozlic
 * @package   BKozlic\ConfigurableOptions
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
declare(strict_types=1);
namespace BKozlic\ConfigurableOptions\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Attributes
 */
class Attributes extends AbstractFieldArray
{
    /**
     * @var AsyncDropdown
     */
    protected $isAsyncDropDown;

    /**
     * Return select with async load options
     *
     * @return AsyncDropdown
     * @throws LocalizedException
     */
    protected function getLoadingTypeDropDown()
    {
        if (!$this->isAsyncDropDown) {
            $this->isAsyncDropDown = $this->getLayout()->createBlock(
                AsyncDropdown::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }

        return $this->isAsyncDropDown;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'selector',
            [
                'label' => __('HTML Element Class or ID'),
                'class' => 'required-entry'
            ]
        );
        $this->addColumn(
            'simple_product_attribute',
            [
                'label' => __('Product\'s Attribute Code'),
                'class' => 'required-entry'
            ]
        );
        $this->addColumn(
            'load_asynchronously',
            [
                'label' => __('Load Value Asynchronously'),
                'renderer' => $this->getLoadingTypeDropDown()
            ]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareArrayRow(DataObject $row)
    {
        $optionExtraAttr = [];
        $optionExtraAttr[
            'option_' . $this->getLoadingTypeDropDown()->calcOptionHash($row->getData('load_asynchronously'))
        ] = 'selected="selected"';

        $row->setData(
            'option_extra_attrs',
            $optionExtraAttr
        );
    }
}
