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

    let configurableMixin = {
        _create: function () {
            this.options.gallerySwitchStrategy = this.options.spConfig.gallerySwitchStrategy;
            this._super();
            this._preselect();
        },

        _configureElement: function (element) {
            this._super(element);
            this._updateSimpleProductAttributes(element);
        },

        /**
         * Preselect configurable product options
         * @private
         */
        _preselect: function () {
            let widget = this,
                options = this.options,
                preselectEnabled = options.spConfig.preselectEnabled,
                simpleProduct = options.spConfig.simpleProduct,
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
                selectOptions = this.options.spConfig.index[simpleProduct];

            if (!selectOptions) {
                this._preselectFirstOptions();
                return false;
            }

            $.each(selectOptions, function (index, value) {
                let attributeId = index,
                    optionId = value,
                    $select = $(widget.options.superSelector + '[name="super_attribute[' + attributeId + ']"]'),
                    $optionElement = $select.find('option[value="' + optionId + '"]').first();

                $select.val($optionElement.val());
                $select.trigger('change');
            });
        },

        /**
         * Preselect first not disabled options of configurable product
         * @private
         */
        _preselectFirstOptions: function () {
            $(this.options.superSelector).each(function () {
                let $select = $(this),
                    $optionElement = $select.find('option:not([disabled])').first();

                if (!$optionElement.val() > 0 || $optionElement.val() !== "") {
                    $optionElement = $optionElement.nextAll('option:not([disabled])').first();
                }

                $select.val($optionElement.val());
                $select.trigger('change');
            });
        },

        /**
         * Update simple product attribute values
         * @private
         */
        _updateSimpleProductAttributes: function (element) {
            let widget = this,
                updateEnabled = widget.options.spConfig.attributesUpdateEnabled,
                options = _.object(_.keys(widget.optionsMap), {}),
                key,
                attributeId = element.config.id;

            options[attributeId] = element.value;

            if (!updateEnabled) {
                return false;
            }

            key = _.findKey(widget.options.spConfig.index, options);
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
            let attributesForUpdate = this.options.spConfig.attributesForUpdate;
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

    return function (configurable) {
        $.widget('mage.configurable', configurable, configurableMixin);
        return $.mage.configurable;
    }
});
