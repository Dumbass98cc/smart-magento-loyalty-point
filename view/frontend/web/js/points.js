define(
    [
        "jquery",
        'Magento_Checkout/js/model/quote',
        "jquery/ui"
    ],
    function ($, quote) {
        'use strict';

        $.widget(
            'mage.rewardPoints',
            {
                options: {
                    "rewardPointSelector": null,
                    "applyButton": null,
                    "points": 0,
                    "maxPoints": 0,
                    "selectedPoints": 0
                },

                totals: null,

                _create: function () {
                    this.rewardPoints = $(this.options.rewardPointSelector);

                    $(this.options.applyButton).on(
                        'click',
                        $.proxy(
                            function () {
                                this.rewardPoints.attr('data-validate', '{required:true}');
                                $(this.element).validation().submit();
                            },
                            this
                        )
                    );
                },

                _apply: function () {

                },
            }
        );

        return $.mage.rewardPoints;
    }
);