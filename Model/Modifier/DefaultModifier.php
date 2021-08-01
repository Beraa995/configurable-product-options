<?php
/**
 * @category  BKozlic
 * @package   BKozlic\ConfigurableOptions
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
declare(strict_types=1);
namespace BKozlic\ConfigurableOptions\Model\Modifier;

use BKozlic\ConfigurableOptions\Model\ModifierInterface;
use Magento\Catalog\Api\Data\ProductInterface;

class DefaultModifier implements ModifierInterface
{
    /**
     * @inheritDoc
     */
    public function processValue(string $attributeCode, ProductInterface $product, string $cssSelector, $value)
    {
        return $value;
    }
}
