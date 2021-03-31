define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Appboxo_Connector/js/model/checkout/order-paymentid-validator'
    ],
    function (Component, additionalValidators, paymentidValidator) {
        'use strict';

        additionalValidators.registerValidator(paymentidValidator);

        return Component.extend({});
    }
);