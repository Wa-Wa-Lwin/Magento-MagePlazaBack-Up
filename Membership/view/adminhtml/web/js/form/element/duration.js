/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license sliderConfig is
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
    'jquery',
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
], function ($, registry, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            duration: false,
            listens: {
                'duration': 'toggleElement'
            }
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super().observe('duration');

            this.toggleElement();

            return this;
        },

        /**
         * Toggle element
         */
        toggleElement: function () {
            this.disableField(['mpmembership_price_fixed']);
            this.enableField(['mpmembership_duration_options']);
        },

        /**
         * @param {Array} fields
         */
        disableField: function (fields) {
            var self   = this,
                parent = registry.get(this.parentName),
                root   = registry.get(parent.parentName).name;

            $.each(fields, function (index, field) {
                registry.async(root + '.container_' + field + '.' + field)(function (elem) {
                    elem.visible(self.duration() !== 'custom');
                    elem.disabled(self.duration() === 'custom');
                });
            });
        },

        /**
         * @param {Array} fields
         */
        enableField: function (fields) {
            var self   = this,
                parent = registry.get(this.parentName),
                root   = registry.get(parent.parentName).name;

            $.each(fields, function (index, field) {
                registry.async(root + '.container_' + field + '.' + field)(function (elem) {
                    elem.visible(self.duration() === 'custom');
                    elem.disabled(self.duration() !== 'custom');
                });
            });
        }
    });
});

