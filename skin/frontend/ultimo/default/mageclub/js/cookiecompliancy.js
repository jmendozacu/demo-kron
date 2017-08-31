/* 
Copyright 2012 Avacari.com | License required for use.
*/
jQuery.noConflict();
(function(jQuery) {
	
	var cookieCompliancy = function(options) {
		
		var ccClick = function() {	
			toggleElement(false);
		}
		
		var toggleElement = function(showModal) {
			
			if(defaults.type == 'top') {
			
				var modalHeight = jQuerymodalElement.outerHeight();
	
				if(jQuerymodalElement.data('active') === true && showModal == false) {
					jQuerymodalElement.data('active', false);
					
					jQueryelement.animate({
						'margin-top': '0'
					}, defaults.modalAnimationTime - 250);
	
					setTimeout(function() {
						jQuerymodalElement.animate({
							'margin-top': '-' + modalHeight + 'px'
						}, defaults.modalAnimationTime);
					}, 250);
				} else {
					jQuerymodalElement.data('active', true);
					jQuerymodalElement.css('margin-top', '-' + modalHeight + 'px');
					
					jQuerymodalElement.animate({
						'margin-top': '0'
					}, defaults.modalAnimationTime);
					
					setTimeout(function() {
						jQueryelement.animate({
						'margin-top': (modalHeight - 1) + 'px'
					}, defaults.modalAnimationTime + 250);
					}, 250)
				}
			} else {
				if(jQuerymodalElement.data('active') === true) {
					jQuerymodalElement.data('active', false);
					
					if(defaults.modalAnimationType == 'appear') {
						jQuerymodalElement.hide();	
					} else {
						jQuerymodalElement.fadeOut(defaults.modalAnimationTime);	
					}
				} else {
					jQuerymodalElement.data('active', true);
					if(defaults.modalAnimationType == 'appear') {
						jQuerymodalElement.show();	
					} else {
						jQuerymodalElement.fadeIn(defaults.modalAnimationTime);	
					}
				}	
			}
		}
		
		var buttonClick = function(elem) {
			
			if(jQuery.cookie(defaults.cookieName) == 'true') {
				setCookie(defaults.cookieName, false);
				window.location = document.URL; //Refresh the page so the user is no longer tracked
			} else {
				setCookie(defaults.cookieName, true);
				appendCookieJS();
				toggleElement(false);
				jQuerymodalElement.find('input').val(defaults.modalButtonTextAccepted);
			}
		}
		
		var setCookie = function(name, value) {
			jQuery.cookie(name, value, {expires: defaults.cookieExpiry, path: '/'});
		}
		
		var appendCookieJS = function() {
			jQuery.ajax({
				url: defaults.pathToJS,
				success: function(js) {
					jQuery('head').append(js);
			  	}
			});	
		}
		
		var moreClick = function(elem) {
			var elem = jQuery(elem);
			
			if(jQuery(elem).data('active') == true) {
				elem.text('Read more...');
				
				jQuerymodalElement.find('#cookieCompliancyLongDesc').slideUp(defaults.modalAnimationTime, function() {
					jQuerymodalElement.find('a.ccreadmore').data('active', false);	
				});	
			} else {
				elem.text('Read less...');
				
				jQuerymodalElement.find('#cookieCompliancyLongDesc').slideDown(defaults.modalAnimationTime, function() {
					jQuerymodalElement.find('a.ccreadmore').data('active', true);	
				});	
			}
		}
		
		var defaults = jQuery.extend({}, jQuery.fn.cookiecompliancy.defaults, options);
		var ccClass = 'cc' + defaults.type + ' cc' + defaults.location + ' cc' + defaults.color;
		
		var bodyHtml = '<div id="cookieCompliancy" class="' + ccClass + '"></div>';
		
		if(defaults.type == 'top') {
			bodyHtml = '<div id="cookieCompliancyCompleteWrapper">' + bodyHtml;	
		}
		
		var firstLoad = jQuery.cookie('cookiecompliancyFirstLoad') == 'false' ? false : true;
		
		var isCookieSet = jQuery.cookie(defaults.cookieName) == 'true' ? true : false;
		
		if(!firstLoad) {
			if(defaults.showAfterFirstLoad == true && isCookieSet == false) {
				var showModal = true;
			} else {
				var showModal = false;	
			}
		} else {
			var showModal = true;	
		}
		
		if(defaults.type == 'top') {
			bodyHtml = bodyHtml + '<div id="cookieCompliancyModal" class="cc' + defaults.type + ' cc' + defaults.location + ' cc' + defaults.color  + ' cc' + defaults.modalMode + '"' + ((showModal == true) ? ' data-active="true">' : ' data-active="false" style="display: none;">') + '<div id="cookieCompliancyModalWrapper" style="width: ' + defaults.contentWidth + 'px;">';
			bodyHtml = bodyHtml + '<div class="ccdesc">' + defaults.desc + '</div><div class="ccbtn">';
			
			if(isCookieSet) {
				bodyHtml = bodyHtml + '<input type="button" value="' + defaults.modalButtonTextAccepted + '" />';
			} else {
				bodyHtml = bodyHtml + '<input type="button" value="' + defaults.modalButtonTextNotAccepted + '" />';	
			}
			
			bodyHtml = bodyHtml + '</div>';
			bodyHtml = bodyHtml + '<div style="clear: both;"></div></div></div></div>';	
		
		} else {
			bodyHtml = bodyHtml + '<div id="cookieCompliancyModal" class="cc' + defaults.type + ' cc' + defaults.location + ' cc' + defaults.color  + ' cc' + defaults.modalMode + '"' + ((showModal == true) ? ' data-active="true">' : ' data-active="false" style="display: none;">');
			bodyHtml = bodyHtml + '<div class="ccCloseBtn"></div>';
			bodyHtml = bodyHtml + '<h2>' + defaults.modalTitle + '</h2>';
			bodyHtml = bodyHtml + '<p>' + defaults.desc + '</p>';
			
			if(isCookieSet) {
				bodyHtml = bodyHtml + '<p>You have already accepted cookies. Click the button below to disable them.</p>';	
			}
			
			bodyHtml = bodyHtml + '<div id="cookieCompliancyLongDesc">' + defaults.modalLongDesc + '</div>';
			if(isCookieSet) {
				bodyHtml = bodyHtml + '<div style="clear: both;"></div><input type="button" value="' + defaults.modalButtonTextAccepted + '" />';
			} else {
				bodyHtml = bodyHtml + '<input type="button" value="' + defaults.modalButtonTextNotAccepted + '" />';	
			}
			bodyHtml = bodyHtml + '<p class="cclast"><a class="ccreadmore" href="javascript:void(0);">Read more...</a></p>';
			bodyHtml = bodyHtml + '</div>';		
		}
		
		if(isCookieSet) {
			appendCookieJS();	
		}

		
		
		
		jQuery('body').append(bodyHtml);
		var jQueryelement = jQuery('#cookieCompliancy');
		var jQuerymodalElement = jQuery('#cookieCompliancyModal');
		
		if(defaults.type == 'top') {
			jQuerymodalElement.css({'display': 'block', 'visibility': 'hidden'});
			var descElem = jQuerymodalElement.find('.ccdesc');
			var descHeight = descElem.outerHeight();	
			
			descElem.css('margin-top', '-' + (descHeight / 2) + 'px');
			jQuerymodalElement.css({'visibility': 'visible', 'margin-top': '-200px'});
			
			if(showModal) {
				setTimeout(function() {
					toggleElement(true);	
				}, 100);
			}
		}
		
		setCookie('cookiecompliancyFirstLoad', false);
		
		jQueryelement.click(function() {
			ccClick();
		});
		
		jQuerymodalElement.find('a.ccreadmore').click(function() {
			moreClick(this);
		});
		
		jQuerymodalElement.find('input').click(function() {
			buttonClick();
		});
		
		jQuerymodalElement.find('.ccCloseBtn').click(function() {
			toggleElement(false);
		});
		
	}
	
	jQuery.fn.cookiecompliancy = function(options) {
		var cc = new cookieCompliancy(options);
	}
	
	jQuery.fn.cookiecompliancy.defaults = {
		type: 'bottom', 
		location: 'left', 
		color: 'blue',
		modalMode: 'dark',
		modalTitle: 'Cookie Compliancy',
		desc: '<p>We use cookies to improve your experience on the website.</p>',
		modalLongDesc: '<p>Some cookies are required to ensure that the site functions correctly, for this reason we may have already set some cookies.</p><p>Other cookies gather anonymous data about how you are using the website, however these will only be set if you give your consent.</p>',
		modalButtonTextAccepted: 'Disable Cookies',
		modalButtonTextNotAccepted: 'Allow Cookies',
		cookieExpiry: 90,
		cookieName: 'cookiecompliancy',
		modalAnimationType: 'fade',
		modalAnimationTime: 500,
		pathToJS: 'cookiecompliancy.html',
		showAfterFirstLoad: false,
		contentWidth: 1000
	};
	
})(jQuery);

/*!
 * jQuery Cookie Plugin
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2011, Klaus Hartl
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.opensource.org/licenses/GPL-2.0
 */
(function(jQuery) {
    jQuery.cookie = function(key, value, options) {

        // key and at least value given, set cookie...
        if (arguments.length > 1 && (!/Object/.test(Object.prototype.toString.call(value)) || value === null || value === undefined)) {
            options = jQuery.extend({}, options);

            if (value === null || value === undefined) {
                options.expires = -1;
            }

            if (typeof options.expires === 'number') {
                var days = options.expires, t = options.expires = new Date();
                t.setDate(t.getDate() + days);
            }

            value = String(value);

            return (document.cookie = [
                encodeURIComponent(key), '=', options.raw ? value : encodeURIComponent(value),
                options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
                options.path    ? '; path=' + options.path : '',
                options.domain  ? '; domain=' + options.domain : '',
                options.secure  ? '; secure' : ''
            ].join(''));
        }

        // key and possibly options given, get cookie...
        options = value || {};
        var decode = options.raw ? function(s) { return s; } : decodeURIComponent;

        var pairs = document.cookie.split('; ');
        for (var i = 0, pair; pair = pairs[i] && pairs[i].split('='); i++) {
            if (decode(pair[0]) === key) return decode(pair[1] || ''); // IE saves cookies with empty string as "c; ", e.g. without "=" as opposed to EOMB, thus pair[1] may be undefined
        }
        return null;
    };
})(jQuery);