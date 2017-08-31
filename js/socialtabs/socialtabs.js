jQuery(function ($) {
    "use strict";
    $(".social-widgets .item").each(function () {
        var $this = $(this),
            timer;
        $this.on("mouseenter",function () {
            var $this = $(this);
            if (timer) clearTimeout(timer);
            timer = setTimeout(function () {
                $this.addClass("active")
            }, 200)
        }).on("mouseleave",function () {
                var $this = $(this);
                if (timer) clearTimeout(timer);
                timer = setTimeout(function () {
                    $this.removeClass("active")
                }, 100)
            }).on("click", function (e) {
                e.preventDefault()
            })
    })
});