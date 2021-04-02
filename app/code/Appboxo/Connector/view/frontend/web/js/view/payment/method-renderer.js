define(
	[
	'uiComponent',
	'Magento_Checkout/js/model/payment/renderer-list'
	],
	function (
		Component,
		rendererList
		) {
		'use strict';
		rendererList.push(
		{
			type: 'miniapppayment',
			component: 'Appboxo_Connector/js/view/payment/method-renderer/miniapppayment'
		}
		);
		return Component.extend({});
	}
);