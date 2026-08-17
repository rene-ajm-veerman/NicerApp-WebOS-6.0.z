sa.shoppingCarts = sa.sc = {
	about : {
		copyright : "Rene AJM Veerman, Amsterdam.nl"
	},
	
	selectProduct : function (id) {
		debugger;
		var ajaxCommand = {
			type : 'GET',
			url : jQuery('#'+id).attr('htmlProduct'),
			success : function (productDescription, ts) {
				debugger;
				jQuery('#productDetails', window.top.document).html(productDescription);
			}
		};
		var r = jQuery.ajax(ajaxCommand);
		jQuery('.saShoppingCarts_Product img').css ({
			border : 0
		});
		jQuery('#'+id+' img').css ({
			border : '2px solid green'
		});
	}
};