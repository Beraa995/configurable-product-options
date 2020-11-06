<?php
/**
 * @category  BKozlic
 * @package   BKozlic\ConfigurableOptions
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\ConfigurableOptions\Model\Modifier;

use BKozlic\ConfigurableOptions\Model\ModifierInterface;

class DefaultModifier implements ModifierInterface
{
    /**
     * @inheritDoc
     */
    public function processValue($attributeCode, $productId, $cssSelector, $value)
    {
        return $value;
    }
}
