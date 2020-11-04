/**
 * @category  BKozlic
 * @package   BKozlic\ConfigurableOptions
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
define([
    'jquery'
], function ($) {
    'use strict';

    var configurableMixin = {
        _create: function () {
            this.options.gallerySwitchStrategy = this.options.spConfig.gallerySwitchStrategy;
            this._super();
        },
    };

    return function (configurable) {
        $.widget('mage.configurable', configurable, configurableMixin);
        return $.mage.configurable;
    }
});
