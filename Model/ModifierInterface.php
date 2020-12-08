<?php
/**
 * @category  BKozlic
 * @package   BKozlic\ConfigurableOptions
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\ConfigurableOptions\Model;

use Magento\Catalog\Api\Data\ProductInterface;

interface ModifierInterface
{
    /**
     * Process attribute value
     * @param string $attributeCode
     * @param ProductInterface $product
     * @param string $cssSelector
     * @param mixed $value
     * @return mixed
     */
    public function processValue(string $attributeCode, ProductInterface $product, string $cssSelector, $value);
}
