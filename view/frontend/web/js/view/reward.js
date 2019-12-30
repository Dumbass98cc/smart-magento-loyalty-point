define([
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'ko'
], function (Component, customerData, ko) {
    'use strict';

    return Component.extend({

        initialize: function () {
            this._super();
            this.reward = customerData.get('reward');
        }
    });
});
