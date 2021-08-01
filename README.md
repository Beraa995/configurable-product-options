# Magento 2 Configurable Options

This module provides functionality for preselecting configurable product options and updating attribute
values from selected simple product.

## Installation

```composer require bkozlic/configurable-options```

```php bin/magento setup:upgrade```

## Usage

### Admin system configuration

#### Go to Admin Panel -> Stores -> Configuration -> Catalog -> Configurable Options

|Option|Functionality|
|-------------|:-------------:|
|Enable|Enable or disable complete module functionality.|
|Simple Product Images|Set if images of selected simple products will be prepended to configurable images or they will replace them.|
|Preselect Product Options|Enable or disable option preselect after product page is loaded.|
|Update Simple Product Attribute|Enable or disable update of attribute values for selected simple product.|
|Attributes|Set as many attributes as you want. You need to specify attribute code, html selector where value will be placed and choose if value should be updated asynchronously or not. Works only if previous option is set to "Yes".|

### Set custom product for preselect

Open configurable product in Admin. Under "Simple Product Preselect" fieldset you can select specific simple product to preselect after product page is loaded.

### Modify attribute values

You can use modifier functionality to modify product's attribute value. Take, for example, the product sku. If you want to display sku value with prefix, suffix or something different you can do this.

Add modifier in the di.xml. There is already default modifier in the module which you can take as an example.
```
<type name="BKozlic\ConfigurableOptions\Model\ValueModifierPool">
    <arguments>
        <argument name="modifiers" xsi:type="array">
            <item name="modifier_name" xsi:type="array">
                <item name="class" xsi:type="object">Vendor\Module\Model\Modifier\ModifierName</item>
                <item name="sortOrder" xsi:type="number">10</item>
            </item>
        </argument>
    </arguments>
</type>
```

Sort order defines in which order modifiers from the pool will be executed.

Modifier class must implement
```\BKozlic\ConfigurableOptions\Model\ModifierInterface```

```
    public function processValue(string $attributeCode, ProductInterface $product, string $cssSelector, $value)
    {
        // Update value
    }
```

### Add custom values

If you want to add value for the products but there is no attribute for it, you can do it with modifier. <br/>
* Add custom non-existing attribute code in the store configuration under Attributes fields.
* Create a custom modifier class and add it to the di.xml.
* In the modifier's processValue function check if attribute code is equal to the code you added in configuration and add value you want to display to the frontend.

By default, product name and sku are updated after simple product is preselected.

## Recommendations
It is recommended to set async load for attributes with a big values and attributes which will be added in the html element not visible until you scroll.

## Prerequisites

* Magento >= 2.3.2

## Developers
* [Berin Kozlic](https://github.com/Beraa995)
