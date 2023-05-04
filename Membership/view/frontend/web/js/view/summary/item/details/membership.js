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
    'underscore',
    'Magento_Checkout/js/view/summary/abstract-total',
    'Mageplaza_Membership/js/model/membership'
], function (_, Component, membership) {
    'use strict';

    return Component.extend({
        getValue: function (item) {
            var product = _.findWhere(membership.config(), {item_id: item.item_id + ''});

            if (!_.isEmpty(product) && product.hasOwnProperty('duration')) {
                return product.duration;
            }

            return '';
        }
    });
});
