
jQuery(document).ready(function($){
	var rule_all_allow_countries = $('#rule_all_allow_countries');
	var sg_countries = $('#rule_sg_countries');
	rule_all_allow_countries.on('click', function(e){
		if(rule_all_allow_countries.val() == '1'){
			sg_countries.prop('disabled', true);
		}else{
			sg_countries.prop('disabled', false);
		}
	})

	if (rule_all_allow_countries.val() == '1' ) {
			sg_countries.prop('disabled', true);
	}else{
		sg_countries.prop('disabled', false);
	}

});