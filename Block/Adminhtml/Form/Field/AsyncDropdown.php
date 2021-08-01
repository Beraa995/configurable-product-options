<?php
/**
 * @category  BKozlic
 * @package   BKozlic\ConfigurableOptions
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
declare(strict_types=1);
namespace BKozlic\ConfigurableOptions\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Html\Select;

/**
 * Class AsyncDropdown
 */
class AsyncDropdown extends Select
{
    /**
     * Set name to the select
     *
     * @param string $value
     *
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        $this->setOptions([
            ['value' => 0, 'label' => 'No', 'params' => []],
            ['value' => 1, 'label' => 'Yes', 'params' => []]
        ]);

        return parent::_toHtml();
    }
}
