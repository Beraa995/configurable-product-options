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

    let swatchRendererMixin = {
        _init: function () {
            this.options.gallerySwitchStrategy = this.options.jsonConfig.gallerySwitchStrategy;
            this._super();
            this._preselect();
        },

        _OnClick: function ($this, $widget, eventName) {
            this._super($this, $widget, eventName);
            this._updateSimpleProductAttributes();
        },

        _OnChange: function ($this, $widget) {
            this._super($this, $widget);
            this._updateSimpleProductAttributes();
        },

        /**
         * Preselect configurable product options
         * @private
         */
        _preselect: function () {
            let widget = this,
                options = this.options,
                preselectEnabled = options.jsonConfig.preselectEnabled,
                simpleProduct = options.jsonConfig.simpleProduct,
                gallery = widget.element.parents('.column.main').find(widget.options.mediaGallerySelector);

            if (!preselectEnabled) {
                return false;
            }

            //@TODO preselect in product lists
            gallery.data('gallery') ?
                widget._preselectProduct(simpleProduct) :
                gallery.on('gallery:loaded', function () {
                    widget._preselectProduct(simpleProduct);
                });
        },

        /**
         * Preselect specific product if set
         * @param simpleProduct
         * @private
         */
        _preselectProduct: function (simpleProduct) {
            let widget = this,
                classes = widget.options.classes,
                selectOptions = this.options.jsonConfig.index[simpleProduct];

            if (!selectOptions) {
                this._preselectFirstOptions();
                return false;
            }

            $.each(selectOptions, function (index, value) {
                let attributeId = index,
                    optionId = value,
                    $wrapper = $('.' + classes.attributeClass + '[attribute-id="' + attributeId + '"]'),
                    $optionsWrapper = $wrapper.find('.' + classes.attributeOptionsWrapper);

                if ($optionsWrapper.children().is('div')) {
                    let $optionElement = $wrapper.find('.' + classes.optionClass + '[option-id="' + optionId + '"]');

                    $optionElement.click();
                } else {
                    let $select = $optionsWrapper.find('select'),
                        $optionElement = $optionsWrapper.find('select option[option-id="' + optionId + '"]');

                    $select.val($optionElement.val());
                    $select.change();
                }
            });
        },


        /**
         * Preselect first not disabled options of configurable product
         * @private
         */
        _preselectFirstOptions: function () {
            let widget = this,
                classes = widget.options.classes;

            $('.' + classes.attributeClass).each(function () {
                let $wrapper = $(this),
                    $optionsWrapper = $wrapper.find('.' + classes.attributeOptionsWrapper);

                if ($optionsWrapper.children().is('div')) {
                    let $optionElement = $wrapper.find('.' + classes.optionClass + ':not([disabled])').first();

                    $optionElement.click();
                } else {
                    let $select = $optionsWrapper.find('select'),
                        $optionElement = $optionsWrapper.find('select option:not([disabled])').first();

                    if (!$optionElement.val() > 0 || $optionElement.val() !== "") {
                        $optionElement = $optionElement.nextAll('option:not([disabled])').first();
                    }

                    $select.val($optionElement.val());
                    $select.change();
                }
            });
        },

        /**
         * Update simple product attribute values
         * @private
         */
        _updateSimpleProductAttributes: function () {
            let widget = this,
                updateEnabled = widget.options.jsonConfig.attributesUpdateEnabled,
                options = _.object(_.keys(widget.optionsMap), {}),
                attributesForUpdate = widget.options.jsonConfig.attributesForUpdate,
                key;

            widget.element.find('.' + widget.options.classes.attributeClass + '[option-selected]').each(function () {
                let attributeId = $(this).attr('attribute-id');
                options[attributeId] = $(this).attr('option-selected');
            });

            if (updateEnabled && attributesForUpdate) {
                key = _.findKey(widget.options.jsonConfig.index, options);
                if (key) {
                    let content = attributesForUpdate[key];

                    for (let i = 0; i < content['length']; i++) {
                        if ($(content['identity'][i]).length && content['value'][i]) {
                            $(content['identity'][i]).html(content['value'][i]);
                        }
                    }
                }
            }
        }
    };

    return function (swatchRenderer) {
        $.widget('mage.SwatchRenderer', swatchRenderer, swatchRendererMixin);
        return $.mage.SwatchRenderer;
    }
});
