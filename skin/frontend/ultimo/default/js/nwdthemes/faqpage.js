(function(jQuery) {

	jQuery(function($){

		var openQuestion = function(hash)
		{
			if ( hash.indexOf('faq_') > -1 ) {
				if ( !$('#faq_'+hash.split('_')[1]).hasClass('open') )
					$('#faq_'+hash.split('_')[1]+' a').click();
				if ( !$(hash).hasClass('open') )
					$(hash+' a').click();

				window.location.hash = hash;
			}
		}

		new jQueryCollapse($("#faqpage"), {
			open: function() {
				this.slideDown(150);
			},
			close: function() {
				this.slideUp(150);
			}
		});
		new jQueryCollapse($(".faqpage_question_wrapper"), {
			open: function() {
				this.slideDown(150);
			},
			close: function() {
				this.slideUp(150);
			}
		});

		//check hash on page load
		openQuestion(window.location.hash);

		$('.faqpage_side_block a').click(function(){
			openQuestion('#'+$(this).attr('href').split('#')[1]);
			return false;
		});

	});

})($nwd_jQuery);