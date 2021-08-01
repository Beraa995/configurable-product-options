/**
 * @category  BKozlic
 * @package   BKozlic\ConfigurableOptions
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
define([
    'mage/url'
], function (url) {
    'use strict';

    return function (productId) {
        let getUrl = url.build('configurableoptions/attributevalues/get/') + 'productId/' + productId;
        return fetch(getUrl, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
    }
});
