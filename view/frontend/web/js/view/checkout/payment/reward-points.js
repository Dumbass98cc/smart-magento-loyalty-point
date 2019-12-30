define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'Loyalty_Point/js/action/set-reward-points',
        'Magento_Checkout/js/model/totals',
        'mage/translate',
        'jquery/ui'
    ],
    function ($, ko, Component, quote, setRewardPointsAction, totals, $t) {
        'use strict';

        var totals = quote.getTotals(),
            rewardPoints = ko.observable(null),
            isApplied = ko.observable(rewardPoints() != null),
            isVisible = ko.observable(null);

        if (totals()) {
            var value = parseInt(window.checkoutConfig.reward.selected_points);
            rewardPoints(value);
            isVisible(window.checkoutConfig.reward.is_visible);
        }

        return Component.extend(
            {
                defaults: {
                    template: 'Loyalty_Point/checkout/payment/reward-points'
                },

                rewardPoints: rewardPoints,

                /**
                 * Applied flag
                 */
                isApplied: isApplied,

                isVisible: isVisible,

                /**
                 * RP application procedure
                 */
                apply: function () {
                    if (this.validate()) {
                        setRewardPointsAction(rewardPoints(), isApplied);
                    }
                },

                /**
                 * Points form validation
                 *
                 * @returns {Boolean}
                 */
                validate: function () {
                    var form = '#reward-points-form';

                    return $(form).validation() && $(form).validation('isValid');
                },

                getCurrentPointsText: function () {
                    return $.mage.__('You have %1 point(s). You can apply maximum %2 point(s).')
                        .replace('%1', window.checkoutConfig.reward.available_points)
                        .replace('%2', window.checkoutConfig.reward.max_points);
                }
            }
        );
    }
);