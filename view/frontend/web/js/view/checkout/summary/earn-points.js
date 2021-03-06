define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/totals'
    ],
    function (Component, quote, totals) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Loyalty_Point/checkout/summary/earn-points'
            },

            totals: quote.getTotals(),

            isDisplayed: function() {
                return this.isFullMode() && this.getPureValue() != 0;
            },

            getPureValue: function() {
                var price = 0;
                if (this.totals()) {
                    price = totals.getSegment('earn_points').value;
                }
                return price;
            }
        });
    }
);