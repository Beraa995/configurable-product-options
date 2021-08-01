/**
 * @category  BKozlic
 * @package   BKozlic\ConfigurableOptions
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
define([
    'jquery',
    'underscore',
    'BKozlic_ConfigurableOptions/js/model/get-async-attribute-values'
], function ($, _, getAsyncValues) {
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
                    $optionsWrapper;

                if (!$wrapper.length) {
                    $wrapper = $('.' + classes.attributeClass + '[data-attribute-id="' + attributeId + '"]');
                }

                $optionsWrapper = $wrapper.find('.' + classes.attributeOptionsWrapper);
                if ($optionsWrapper.children().is('div')) {
                    let $optionElement = $wrapper.find('.' + classes.optionClass + '[option-id="' + optionId + '"]');
                    if (!$optionElement.length) {
                        $optionElement = $wrapper.find('.' + classes.optionClass + '[data-option-id="' + optionId + '"]');
                    }

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
                key;

            widget.element.find('.' + widget.options.classes.attributeClass).each(function () {
                if (!$(this).attr('data-option-selected') && !$(this).attr('option-selected')) {
                    return;
                }

                let attributeId = $(this).attr('data-attribute-id') || $(this).attr('attribute-id'),
                    selectedValue = $(this).attr('data-option-selected') || $(this).attr('option-selected');

                if (selectedValue) {
                    options[attributeId] = selectedValue.toString();
                }
            });

            if (!updateEnabled) {
                return false;
            }

            key = _.findKey(widget.options.jsonConfig.index, options);
            if (!key) {
                return false;
            }

            this._updateAttributeValuesFromJson(key);
            this._updateAttributeValuesAsynchronously(key);
        },

        /**
         * Update simple product attribute values from json
         * @param productId
         * @private
         */
        _updateAttributeValuesFromJson: function (productId) {
            let attributesForUpdate = this.options.jsonConfig.attributesForUpdate;
            if (!attributesForUpdate) {
                return false;
            }

            let content = attributesForUpdate[productId];
            this._addValuesToHtmlElements(content);
        },

        /**
         * Update simple product attribute values asynchronously
         * @param productId
         * @private
         */
        _updateAttributeValuesAsynchronously: function (productId) {
            let response = getAsyncValues(productId),
                widget = this;

            response
                .then(data => data.json())
                .then(result => {
                    if (result.success) {
                        widget._addValuesToHtmlElements(result.data);
                    }
                });
        },

        /**
         * Add values to the html elements
         * @param content
         * @private
         */
        _addValuesToHtmlElements: function (content) {
            $.each(content, function (index, item) {
                if ($(item.selector).length) {
                    $(item.selector).html(item.value);
                }
            });
        }
    };

    return function (swatchRenderer) {
        $.widget('mage.SwatchRenderer', swatchRenderer, swatchRendererMixin);
        return $.mage.SwatchRenderer;
    }
});
