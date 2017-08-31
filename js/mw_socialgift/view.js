jQuery(document).ready(function($){

    if (typeof FACEBOOK_ID == 'undefined') {
        FACEBOOK_ID = '';
    }

    // fancybox
    $(".fancybox").fancybox({
            // 'autoSize': false,
            'minWidth': '480px',
            'minHeight': '200px',
            'maxWidth' : '690px',
            'padding': '0px',
            'autoScale': true,
            'transitionIn': 'fade',
            'transitionOut': 'fade',
            'showCloseButton': true,
            'autoCenter': true,
            'type': 'inline',
            'href': '#socialgift-popup',
            'wrapCSS': 'socialgift-popup',
            helpers : {
                title : null            
            }  
        });
       
    fancybox_run();

    jQuery('#sg-cart-table').on('click', 'li', function(e){
        jQuery('#sg-cart-table').find("input[type='radio']").removeAttr('checked');
        jQuery(this).find("input[type='radio']").prop("checked", true);


        var button = jQuery('#socialgift-share button, #socialgift-share #button');
        if (button.hasClass('gplus-share')) {
            jQuery('#sg_gplus #widget-div').empty();
            var new_button = '<div id="widget-div"  class="g-plusone" data-href="#" data-callback="" ></div>';
            jQuery('#sg_gplus').html(new_button);
            jQuery('#socialgift-share button').hide();
            jQuery('#socialgift-share #sg_gplus').show();
            up_date_gplus();
            gapi.plusone.render('widget-div');
        };
    });


    // facebook prepare data to share
    jQuery('#socialgift-share button, #socialgift-share #button').on('click', function(e) {
        e.preventDefault();
        var input_checked = $('#sg-cart-table').find('input[name=chose_product]:checked');
        if(typeof(input_checked.val()) != 'undefined') {
            var sg_product_share = input_checked.closest('li');
            var href, link, caption, picture, name, description, product_id;
            product_id = sg_product_share.find('input[name=product_id]').attr('value');
            href = sg_product_share.find('.product_image a').attr('href');
            link = sg_product_share.find('.product_image a').attr('href');
            price = sg_product_share.find('.product_price span.price').text();
            caption = sg_product_share.find('.product_image a').attr('title') + ' ' + price;
            name = sg_product_share.find('.product_image a').attr('title') + ' ' + price;
            picture = sg_product_share.find('.product_image img').attr('src');
            href = sg_product_share.find('.product_image a').attr('title');
            description = sg_product_share.find('.product_description p').text();
            if( (typeof(product_id) != 'undefined') &&  (typeof(href) != 'undefined') && (typeof(name) != 'undefined') && (typeof(description) != 'undefined')) {
                // var share_type = $(this).attr('class');
                if ($(this).hasClass('facebook-share')) {
                    fbOpenPopup( href, link, caption, picture, name, description, product_id );
                }else if($(this).hasClass('twitter-share')){
                    e.preventDefault();
                    twOpenPopup(link,caption,product_id);
                }else if($(this).hasClass('gplus-share')){
                    e.preventDefault();
                    gplusOpenPopup(link,caption,product_id);
                }else{
                    console.log("Nothing share type");
                    return true;
                };
            }
        }else{
            alert("Please chose one product!");
        }
    });

    //update billing address
    if (jQuery('#billing\\:country_id').length) {
        jQuery('#billing\\:country_id').on('change', function(){
            if(jQuery.inArray(this.value, COUNTRY_AVAILABLES.split(',')) == -1){
               jQuery('.socialgift_rules_container').hide();
            }else{
               jQuery('.socialgift_rules_container').show();
            }
        });
    };
});

//submit button process
function facebook_share(){
    update_share_class('facebook-share');
};
function twitter_share(){
    update_share_class('twitter-share');
};
function gplus_share(){
    update_share_class('gplus-share');
};
function update_share_class(name){
    jQuery('#sg-cart-table').find("input[type='radio']").removeAttr('checked');
    jQuery('#socialgift-share button').show();
    jQuery('#socialgift-share a#button').hide();
    jQuery('#socialgift-share #sg_gplus').hide();
    jQuery('#socialgift-share button').removeAttr('class');
    jQuery('#socialgift-share button').addClass('button');
    jQuery('#socialgift-share button').addClass(name);

    if(name == 'twitter-share'){
        jQuery('#socialgift-share a#button').removeAttr('class');
        jQuery('#socialgift-share button').addClass('button');
        jQuery('#socialgift-share a#button').addClass('twitter-share');

        jQuery('#socialgift-share a#button').css('background', '#55ACEE');
        jQuery('#socialgift-popup .page-title h1').css('background', '#55ACEE');
        jQuery('#socialgift-share button').hide();
        jQuery('#socialgift-share a#button').show();
    }
    if(name == 'gplus-share'){
        jQuery('#socialgift-share button').css('background', '#dd4b39');
        jQuery('#socialgift-popup .page-title h1').css('background', '#dd4b39');
    }
    if(name == 'facebook-share'){
        jQuery('#socialgift-share button').css('background', '#4c66a4');
        jQuery('#socialgift-popup .page-title h1').css('background', '#4c66a4');
    }
}

//twiiter process
// twitter library
jQuery.getScript("http://platform.twitter.com/widgets.js", function(){

});
function twOpenPopup(url,text,unique_id){
    twttr.events.bind('tweet',function(event){
        jQuery('#socialgift-share .twitter-share').css('background', '#ccc');
        jQuery('#socialgift-share .twitter-share').text('Waitting');
        jQuery.post(mw_baseUrl+"socialgift/index/ajax", {post_id: 'shared', product_id: unique_id } , function(data){
            getGiftAfterShare();
        });
    });
    var twturl="https://twitter.com/intent/tweet?url="+encodeURIComponent(url) + 
        "&count=none&source=tweetbutton&text=" + encodeURIComponent(text) + "&original_referer=" + encodeURIComponent(url);
    document.getElementById("button").href = twturl;
    return true;
}

// google plus share button process
function gplusOpenPopup(url,text,unique_id){
    var win = window.open('https://plus.google.com/share?url='+url, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=400,width=600');
    var interval = window.setInterval(function() {
        try {
            if (win == null || win.closed) {
                window.clearInterval(interval);
            }
        }
        catch (e) {
        }
    }, 1000);
    return win;
}

//google plus +1 button process
function up_date_gplus(){
    var input_checked = jQuery('#sg-cart-table').find('input[name=chose_product]:checked');
    var sg_product_share = input_checked.closest('li');
    var href, link, caption, product_id;
    product_id = sg_product_share.find('input[name=product_id]').attr('value');
    href = sg_product_share.find('.product_image a').attr('href');
    link = sg_product_share.find('.product_image a').attr('href');

    var sg_gplus = jQuery("#sg_gplus div");
    sg_gplus.attr('data-href', link);
    sg_gplus.attr('data-callback', 'googlepluscallback');
    gapi.plusone.render("widget-div" );
    gapi.plusone.go();
}
function googlepluscallback(jsonParam){
    if (typeof(jsonParam) != 'undefined') {
        console.log("URL: " + jsonParam.href + " state: " + jsonParam.state);
        if(jsonParam.state == 'on'){
            var input_checked = jQuery('#sg-cart-table').find('input[name=chose_product]:checked');
            var sg_product_share = input_checked.closest('li');
            unique_id = sg_product_share.find('input[name=product_id]').attr('value');

            jQuery('#socialgift-share button').css('background', '#ccc');
            jQuery('#socialgift-share button').text('Waitting');

            jQuery.post(mw_baseUrl+"socialgift/index/ajax", {post_id: 'shared', product_id: unique_id } , function(data){
                getGiftAfterShare();
            });
        }
    }else{
        alert("undefined");
    }
}

//facebook library
(function (d) {
    var js, id = 'facebook-jssdk';
    if (d.getElementById(id)) {
        return;
    }
    js = d.createElement('script');
    js.id = id;
    js.async = true;
    js.src = "//connect.facebook.net/en_US/all.js";
    d.getElementsByTagName('head')[0].appendChild(js);
}(document));
// facebook

window.fbAsyncInit = function() {
    FB.init({appId: FACEBOOK_ID, status: true, cookie: true, xfbml: true});
};

//facebook process
function fbOpenPopup( href, link, caption, picture, name, description, product_id ) {
    FB.ui(
        {
            method: 'feed',
            href: href,
            app_id: FACEBOOK_ID,
            link: link,
            caption: caption,
            picture: picture,
            name: name,
            description: description
        },
        function(response) {
            if (response && !response.error_code) {
                // alert('Posting completed.'+response['post_id'] );
                jQuery('#socialgift-share button').removeAttr('class');
                jQuery('#socialgift-share button').css('background', '#ccc');
                jQuery('#socialgift-share button').text('Waitting');
                jQuery.post(mw_baseUrl+"socialgift/index/ajax", {post_id: response['post_id'], product_id: product_id } , function(data){
                    getGiftAfterShare();
                });
            } else {
                //alert('Error while posting.');
                /*jQuery.post(mw_baseUrl+"socialgift/index/ajax", {post_id: 'test', product_id: product_id}, function(data){
                    getGiftAfterShare();
                });*/
            }
        }
    );    
}

function getGiftAfterShare(){
    jQuery.fancybox.showLoading();
    jQuery.post(mw_baseUrl+"socialgift/index/getGift", {}, function(data){
        if(data){
            jQuery('#socialgift-popup').empty().html(jQuery.parseJSON(data).html);
            fancybox_run();
            jQuery('.sg-mesage-social').empty().html(SOCIALGIFT_MESSAGE);
            jQuery('ul.messages').html('<li class="success-msg"><ul><li>'+SOCIALGIFT_MESSAGE+'</li></ul></li></ul>');
            jQuery(".fancybox").trigger('click');
            jQuery.fancybox.hideLoading();
        }
    });
}

function fancybox_run(){

    /*
    Carousel initialization
    */
    jQuery('.jcarousel')
        .jcarousel({
            // Options go here
        });

    /*
     Prev control initialization
     */
    jQuery('.jcarousel-control-prev')
        .on('jcarouselcontrol:active', function() {
            jQuery(this).removeClass('inactive');
        })
        .on('jcarouselcontrol:inactive', function() {
            jQuery(this).addClass('inactive');
        })
        .jcarouselControl({
            // Options go here
            target: '-=1'
        });

    /*
     Next control initialization
     */
    jQuery('.jcarousel-control-next')
        .on('jcarouselcontrol:active', function() {
            jQuery(this).removeClass('inactive');
        })
        .on('jcarouselcontrol:inactive', function() {
            jQuery(this).addClass('inactive');
        })
        .jcarouselControl({
            // Options go here
            target: '+=1'
        });

        jQuery('#socialgift-gift button.btn-cart').on('click', function(e) {
            e.preventDefault();
            jQuery('#socialgift-gift').find('button.btn-cart').removeAttr('onclick');
            jQuery('#socialgift-gift').find('button.button').css({'background':'#ccc','border':'none'});
            jQuery('#socialgift-gift').find('button.button span').css({'background':'#ccc','border':'none'});
            jQuery('#socialgift-gift').find('button.btn-cart span span').text('Waiting');
        })
}