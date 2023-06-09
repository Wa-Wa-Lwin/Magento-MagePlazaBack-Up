/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Membership
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define([
    'ko',
    'Magento_Checkout/js/model/quote'
], function (ko, quote) {
    "use strict";

    var config = ko.computed(function () {
        var extensionAttributes = quote.getTotals()().extension_attributes;

        if (extensionAttributes && extensionAttributes.hasOwnProperty('mp_membership')) {
            return JSON.parse(extensionAttributes.mp_membership);
        }

        return {};
    });

    return {
        config: config
    };
});

