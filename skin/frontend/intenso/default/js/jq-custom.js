jQuery(document).ready(function(){

});
 var existProduct= [];
function loadFinanceDetails_price(obj,product_type, cost_of_goods, deposit_percentage, deposit_amount,product_id,for_details) {
		if (existProduct[product_id] == undefined && jQuery(".pay4later_details_" + product_id) != []) {
			existProduct[product_id] = true;
			var my_fd_obj = new FinanceDetails(product_type, cost_of_goods, deposit_percentage, deposit_amount);
			jQuery("#cost_per_month_" + product_id).html("<div>OR</div> &#163;" + my_fd_obj.m_inst + " per month");
			if (for_details == 1) {
				console.log(jQuery(".pay4later_details_" + product_id)[0]);
				jQuery(obj).parent().find(".price-box").append(jQuery(".pay4later_details_" + product_id)[0]);
				jQuery(obj).parent().find(".short-description").append(jQuery(".pay4later_wrapper")[0]);
				jQuery(obj).parent().find(".short-description").append(jQuery(".desktop_pay4later_view")[0]);
			}
			else {
				console.log('else');
				jQuery(".price-box").append(jQuery(".pay4later_details_" + product_id));
				jQuery(".short-description").append(jQuery(".pay4later_wrapper"));
				jQuery(".short-description").append(jQuery(".desktop_pay4later_view"));
			}
		}
	}
function checkJ2tCloneText() {
	if ($('j2t-points-clone') && $$(".j2t-loyalty-points").length > 0) {
		$('j2t-points-clone').style.display = $$(".j2t-loyalty-points")[0].style.display;
		var text_clone = $$(".j2t-loyalty-points")[0].innerHTML;
		text_clone = text_clone.replace("j2t-pts", "j2t-pts-clone");
		text_clone = text_clone.replace("j2t-point-equivalence", "j2t-point-equivalence-clone");
		$('j2t-points-clone').innerHTML = text_clone;
	}
}

jQuery(document).ready(function() {
    jQuery(".right-off-canvas-toggle, .exit-off-canvas").click(function(event) {
        // this.append wouldn't work
        jQuery('.social-widgets').toggleClass("social-widgets-move-right");
    });
});