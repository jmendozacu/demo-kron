define(["json2", "jquery", "backbone", "underscore", "custombox", "easing", "canvas_loader", "jquery_tooltip", "jcarousel", "bcarousel", "iosOverlay", "scrollTo"], function() {
    (function(mw_jquery){
        window.FreeGift = {
            Models: {},
            Collections: {},
            Views: {}
        };

        _.extend(window.FreeGift, Backbone.Events);

        requirejs(["js/mw_freegift/view"]);
    })(jQuery.noConflict());
});