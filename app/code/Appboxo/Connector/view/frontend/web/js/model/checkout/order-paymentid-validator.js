define(
    [
        'jquery',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
        'mage/url',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Ui/js/model/messageList',
        'mage/translate'		
    ],
	function ($, customer, quote, urlBuilder, urlFormatter, errorProcessor, messageContainer, __) {			
        'use strict';

        return {

            /**
             * Make an ajax PUT request to save order paymentid in the quote.
             *
             * @returns {Boolean}
             */
            validate: function () {
                var isCustomer = customer.isLoggedIn();
                var form = $('.payment-method input[name="payment[method]"]:checked').parents('.payment-method').find('form.order-paymentid-form');

                var quoteId = quote.getQuoteId();
                var url;
				
                // validate max length
                var paymentid = form.find('.input-text.order-paymentid').val();
                if (this.hasMaxPaymentIdLength() && paymentid.length > this.getMaxPaymentIdLength()) {
                    messageContainer.addErrorMessage({ message: __("The order paymentid entered exceeded the limit") });
                    return false;
                }
				
                if (isCustomer) {
                    url = urlBuilder.createUrl('/carts/mine/set-order-paymentid', {})
                } else {
                    url = urlBuilder.createUrl('/guest-carts/:cartId/set-order-paymentid', {cartId: quoteId});
                }

                var payload = {
                    cartId: quoteId,
                    orderPaymentId: {
                        paymentid: paymentid
                    }
                };

                if (!payload.orderPaymentId.paymentid) {
                    return true;
                }

                var result = true;

                $.ajax({
                    url: urlFormatter.build(url),
                    data: JSON.stringify(payload),
                    global: false,
                    contentType: 'application/json',
                    type: 'PUT',
                    async: false
                }).done(
                    function (response) {
                        result = true;
                    }
                ).fail(
                    function (response) {
                        result = false;
                        errorProcessor.process(response);
                    }
                );

                return result;
            },	
	
            /**
             * Is order paymentid has max length
             *
             * @return {Boolean}
             */			
            hasMaxPaymentIdLength: function() {
                 return window.checkoutConfig.max_length > 0;
            },
			
            /**
             * Retrieve order paymentid length limit
             *
             * @return {String}
             */			
            getMaxPaymentIdLength: function () {
                 return window.checkoutConfig.max_length;
            }		
        };
    }
);