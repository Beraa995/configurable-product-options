<?php
/**
 * @category  BKozlic
 * @package   BKozlic\ConfigurableOptions
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
declare(strict_types=1);
namespace BKozlic\ConfigurableOptions\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class GallerySwitch
 */
class GallerySwitch implements OptionSourceInterface
{
    /**
     * Return option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'prepend', 'label' => __('Prepend to configurable images')],
            ['value' => 'replace', 'label' => __('Replace configurable images')]
        ];
    }
}
