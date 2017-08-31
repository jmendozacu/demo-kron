window.withoutIE = false;

if(navigator.appName.indexOf("Internet Explorer") != -1){

    window.withoutIE = (
            navigator.appVersion.indexOf("MSIE 6") > -1 ||
            navigator.appVersion.indexOf("MSIE 7") > -1 ||
            navigator.appVersion.indexOf("MSIE 8") > -1 ||
            navigator.appVersion.indexOf("MSIE 9") > -1
    );
}
if(version_num == '1.6'){
    delete window['console'];
}
requirejs.config({
    /*"urlArgs": "v=" + (new Date()).getTime(),*/
    "baseUrl": mw_baseUrl,
    waitSeconds: 0,
    "paths": {
        backbone            : 'js/mw_freegift/lib/backbone-min',
        underscore          : 'js/mw_freegift/lib/underscore',
        product             : 'js/varien/product',
        configurable        : 'js/varien/configurable',
        bundle              : 'skin/frontend/base/default/js/bundle',
    },
    "shim": {
        json2: {},
        jquery: {
            exports: 'mw_jquery'
        },
        jquery_tooltip: {
            deps: ['jquery']
        },
        scrollTo: {
            deps: ['jquery']
        },
        iosOverlay: {
            deps: ['jquery']
        },
        spin: {
            deps: ['jquery'],
        },
        jcarousel: {
            deps: ['jquery']
        },
        bcarousel: {
            deps: ['jcarousel']
        },
        easing: {
            deps: ['jquery']
        },
        jquery_form: {
            deps: ['jquery']
        },
        backbone: {
            deps: ['jquery', 'underscore'],
            exports: 'Backbone'
        },
        custombox: {
            deps: ['backbone']
        },
        underscore: {
            exports: '_'
        },
        canvas_loader: {
            deps: ['jquery']
        },
        tmpl: {
            deps: ["jquery"] /* Load after jquery */
        },
        jquery_mousewheel: {
            deps: ["jquery"]
        },
        jquery_mwheelIntent: {
            deps: ["jquery_mousewheel"]
        },
        jscrollpane: {
            deps: ["jquery_mwheelIntent"]
        }
    }
});
requirejs(["js/mw_freegift/main"]);


