define(
    [
        'jquery',
        'uiComponent',
		'knockout'
    ],
    function ($, Component, ko) {
        'use strict';

		/**
		 * @param {Function} target
		 * @param {String} maxLength
		 * @return {*}
		 */
        ko.extenders.maxPaymentIdLength = function (target, maxLength) {
            var timer;
			
            var result = ko.computed({
                read: target,
                write: function (val) {
                    if (maxLength > 0) {
                        clearTimeout(timer);
                        if (val.length > maxLength) {
                            var limitedVal = val.substring(0, maxLength);
                            if (target() === limitedVal) {
                                target.notifySubscribers();
                            } else {
                                target(limitedVal);
                            }
                            result.css("_error");
                            timer = setTimeout(function () { result.css(""); }, 800);
                        } else {
                            target(val);
                            result.css("");
                        }
                    } else {
                        target(val);
                    }
                }
            }).extend({ notify: 'always' });
			
            result.css = ko.observable();
            result(target());
			
            return result;
        };


        return Component.extend({
            defaults: {
                template: 'Appboxo_Connector/checkout/order-paymentid-block'
            },

            initialize: function() {
                this._super();
                var self = this;
				this.paymentid = ko.observable("").extend(
					{
						maxPaymentIdLength: this.getMaxPaymentIdLength()
					}
				);
                this.remainingCharacters = ko.computed(function(){
                    return self.getMaxPaymentIdLength() - self.paymentid().length;
                });
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
            },

            getDefaultPaymentIdFieldState: function() {
                return window.checkoutConfig.order_paymentid_default_state;
            },
			
            isDefaultPaymentIdFieldStateExpanded: function() {
                return this.getDefaultPaymentIdFieldState() === 1
            }			
        });
    }
);
