jQuery(function($){
$("#banner-slider-demo-13").owlCarousel({autoPlay:true,stopOnHover: true,pagination: false, autoPlay: true,navigation: true,navigationText:["<i class='icon-chevron-left'></i>","<i class='icon-chevron-right'></i>"],slideSpeed : 500,paginationSpeed : 500,singleItem:true,transitionStyle : "fade"});
$(".main-container").remove();
$("#men_product .filter-products .owl-carousel").owlCarousel({
                        lazyLoad: true,
                        itemsCustom: [ [0, 1], [320, 1], [480, 1],[640, 2], [768, 2] ],
                        responsiveRefreshRate: 50,
                        slideSpeed: 200,
                        paginationSpeed: 500,
                        scrollPerPage: false,
                        stopOnHover: true,
                        rewindNav: true,
                        rewindSpeed: 600,
                        pagination: true,
                        navigation: false,
                        autoPlay: true
                   });
$("#women_product .filter-products .owl-carousel").owlCarousel({
                        lazyLoad: true,
                        itemsCustom: [ [0, 1], [320, 1], [480, 1],[640, 2], [768, 2] ],
                        responsiveRefreshRate: 50,
                        slideSpeed: 200,
                        paginationSpeed: 500,
                        scrollPerPage: false,
                        stopOnHover: true,
                        rewindNav: true,
                        rewindSpeed: 600,
                        pagination: true,
                        navigation: false,
                        autoPlay: true
                    });
$("#fashion_product .filter-products .owl-carousel").owlCarousel({
                        lazyLoad: true,
                        itemsCustom: [ [0, 1], [320, 1], [480, 1],[640, 2], [768, 3], [992, 4], [1200, 5]],
                        responsiveRefreshRate: 50,
                        slideSpeed: 200,
                        paginationSpeed: 500,
                        scrollPerPage: false,
                        stopOnHover: true,
                        rewindNav: true,
                        rewindSpeed: 600,
                        navigation: false,
                        pagination: false,
                        autoPlay: true
                    });
$("#half-image-1").css("min-height",$("#half-content-1").outerHeight()+"px");
        $("#half-image-2").css("min-height",$("#half-content-2").outerHeight()+"px");
    setTimeout(function(){
        $("#half-image-1").css("min-height",$("#half-content-1").outerHeight()+"px");
        $("#half-image-2").css("min-height",$("#half-content-2").outerHeight()+"px");
    }, 5000);
    $(window).resize(function(){
        setTimeout(function(){
            $("#half-image-1").css("min-height",$("#half-content-1").outerHeight()+"px");
            $("#half-image-2").css("min-height",$("#half-content-2").outerHeight()+"px");
        }, 500);
    });
});