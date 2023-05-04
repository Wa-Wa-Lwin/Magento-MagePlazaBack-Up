/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiComponent',
    'Magento_Customer/js/customer-data'
], function (Component, customerData) {
    'use strict';

    return Component.extend({
        /** @inheritdoc */
        initialize: function () {
            this._super();

            var sections = ['wishlist'];
            customerData.invalidate(sections);
            customerData.reload(sections, true);
            this.wishlist = customerData.get('wishlist');

        }
    });
});
