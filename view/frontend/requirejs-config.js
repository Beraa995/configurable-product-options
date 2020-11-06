/**
 * @category  BKozlic
 * @package   BKozlic\ConfigurableOptions
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
var config = {
    config: {
        mixins: {
            'Magento_Swatches/js/swatch-renderer': {
                'BKozlic_ConfigurableOptions/js/swatch-renderer-mixin': true
            },
            'Magento_ConfigurableProduct/js/configurable': {
                'BKozlic_ConfigurableOptions/js/configurable-mixin': true
            }
        }
    }
};
