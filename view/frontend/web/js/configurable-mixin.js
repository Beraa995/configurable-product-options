/**
 * @category  BKozlic
 * @package   BKozlic\ConfigurableOptions
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
define([
    'jquery',
    'underscore'
], function ($, _) {
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
                attributesForUpdate = widget.options.spConfig.attributesForUpdate,
                key,
                attributeId = element.config.id;

            options[attributeId] = element.value;

            if (!updateEnabled || !attributesForUpdate) {
                return false;
            }

            key = _.findKey(widget.options.spConfig.index, options);
            if (!key) {
                return false;
            }

            let content = attributesForUpdate[key];
            $.each(content, function (index, item) {
                if ($(item.identity).length) {
                    $(item.identity).html(item.value);
                }
            });
        },
    };

    return function (configurable) {
        $.widget('mage.configurable', configurable, configurableMixin);
        return $.mage.configurable;
    }
});
