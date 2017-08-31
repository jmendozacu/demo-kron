var Popup = Class.create();
Popup.prototype = {
	initialize: function(popupId, backgroundId, params){
        this.popupId = popupId;
        this.backgroundId = backgroundId;
        if (params.cityCount) {
	        this.cityCount = params.cityCount;
	    }
        if (params.lastTab) {
       		this.lastTab = params.lastTab;
       	}       	
        if (params.mainDealUrl) {
	        this.mainDealUrl = params.mainDealUrl;
	    }
    },	
	
	showPopup: function() {
		if (!this.cityCount || this.cityCount>1) {
			if ($(this.popupId)) {
				$(this.popupId).appear({ duration: 0.7 });
			}
			if (this.backgroundId && $(this.backgroundId)) {
				$(this.backgroundId).appear({ duration: 0.7 });
			}
		} else {
			window.location = this.mainDealUrl;
		}
	},

	hidePopup: function(){
		$(this.popupId).fade({ duration: 0.7 });
		if (this.backgroundId) {
			$(this.backgroundId).fade({ duration: 0.7 });
		}
	},	
	
	//popup tab functions
	nextTab: function(tabName) {
		$(this.lastTab).className = 'tab';		
		if ($('universal')) {
			$('universal').className = 'tab';
		}
		$(tabName).className='tab_hover';
		
		Element.hide(this.lastTab+'_data');
		if (tabName!='universal') {
			Element.show(tabName+'_data');
		}
		this.lastTab=tabName;
	}
}

var Gift = Class.create();
Gift.prototype = {
	initialize: function(form, urls){
        this.form = form;
        this.saveUrl = urls.saveUrl;
        this.failureUrl = urls.failureUrl;
        this.onSave = this.save.bindAsEventListener(this);
        
        if ($(this.form)) {
            $(this.form).observe('submit', function(event){this.saveGift();Event.stop(event);}.bind(this));
        }
        
        if ($('coupon_to_email') && $('coupon_to_email').value=='' && $('coupon_from').value!=''){
			$('email_radio2').checked='checked';
			$('coupon_to_email').disabled='disabled';
			$('coupon_to_email').removeClassName('required-entry');
		}
    },	

	enableEmail: function() {
		$('coupon_to_email').disabled='';
		$('coupon_to_email').addClassName('required-entry');
		$('coupon_to_email').addClassName('validate-email'); 	
	},	

	disableEmail: function() {
		$('coupon_to_email').disabled='disabled';
		$('coupon_to_email').removeClassName('required-entry'); 
		$('coupon_to_email').removeClassName('validate-email'); 		
		if ($('advice-required-entry-coupon_to_email')) {
			$('advice-required-entry-coupon_to_email').style.display='none';
		}
	},	
	
	checkMaxLength: function(Object, MaxLen) {
        if (Object.value.length > MaxLen-1) {
            Object.value = Object.value.substr(0, MaxLen);
        }
        return 1;
    },
	
	updateButtons: function(){	
		Element.show('gift-buttons');
		Element.hide('gift-please-wait');
		Element.show('gift-link');
		Element.hide('gift-link-please-wait');	
	},
        
	save: function(transport){
		if (transport && transport.responseText){
            try{
                response = eval('(' + transport.responseText + ')');
            }
            catch (e) {
            	alert(e);
                response = {};
            }
        }

        if (response.error){
            alert(response.message);				
            this.updateButtons();
            return false;
        }
        
        if (this.type=='remove') {
			$('coupon_from').value='';
			$('coupon_to').value='';
			$('coupon_to_email').value='';
			$('coupon_message').value='';
		}
		
		if (response.update_section) {
			$(response.update_section.name).update(response.update_section.html);
		}
							
        this.updateButtons();	
		giftPopup.hidePopup();
	},	
	
	failure: function(){	
		location.href = this.failureUrl;
	},
	
	removeGift: function(){
		var params = new Array();
	    params['coupon_from'] = '';
	    params['coupon_to'] = '';
	    params['coupon_to_email'] = '';
	    params['coupon_message'] = '';
	    this.type = 'remove';
		Element.hide('gift-link');
		Element.show('gift-link-please-wait');
        var request = new Ajax.Request(
        	this.saveUrl,
        	{
    	        method:'post',
    	        onSuccess: this.onSave,
    	        onFailure: this.failure.bind(this),
    	        parameters: params
    	    }
    	);    	
    },
	
	saveGift: function(){
        var validator = new Validation(this.form);
        if (validator.validate()) {
			Element.hide('gift-buttons');
			Element.show('gift-please-wait');
			this.type = 'save';
        	var request = new Ajax.Request(
        	    this.saveUrl,
        	    {
        	        method:'post',
        	        onSuccess: this.onSave,
        	        onFailure: this.failure.bind(this),
        	        parameters: Form.serialize(this.form)
        	    }
        	);
        }
    }
}

