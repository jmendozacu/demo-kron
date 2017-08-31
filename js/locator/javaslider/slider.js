if (typeof Orange35 == 'undefined') {
    var Orange35 = {};
}
Orange35.Swiper = Class.create();
Orange35.Swiper.prototype = {
    touchstart: {x: -1, y: -1},
    touchDiff: {x: -1, y: -1},
    touchmove: {x: -1, y: -1},
    touchend: false,
    orientation: '',
    slider: {},
    enable: false,
    element: null,
    initialize: function (element, slider, orientation) {
        if (typeof (slider) != 'undefined') {
            this.slider = slider;
            this.orientation = orientation;
        }
        if (typeof(element) != 'undefined') {
            this.enable = true;
            this.element = element;
            element.addEventListener('touchmove', this.touchHandler.bind(this), false);
            element.addEventListener('touchend', this.touchHandler.bind(this), false);
            if (this.orientation == 'vertical') {
                this.reInit();
            }
        } else {
            console.log('element for touch is undefined');
        }
    },
    touchHandler: function (event) {
        if (this.enable != true){
            return true;
        }
        var touch;
        if (typeof event !== 'undefined') {
            if (typeof event.touches !== 'undefined') {
                touch = event.changedTouches[0];
                switch (event.type) {
                    case 'touchmove':
                        if (this.touchend == false && (this.touchmove.x == -1 || this.touchmove.y == -1)) {
                            this.touchmove.x = touch.pageX;
                            this.touchmove.y = touch.pageY;
                        }
                        if(this.orientation == 'vertical'){
                            event.preventDefault();
                            return false;
                        }
                        return true;
                        break;
                    case 'touchend':
                        var direction = this.checkDirectionAndActivateHandler(touch);
                        if (direction) {
                            this.touchend = true;
                            event.preventDefault();
                            this.doMove(direction);
                            return false;
                        } else {
                            this.touchend = false;
                            return true;
                        }
                        break;
                    default:
                        break;
                }
            }
        }
    },
    checkDirectionAndActivateHandler: function (touch) {
        if (this.touchmove.x != -1 || this.touchmove.y != -1) {

            if (this.orientation == 'horizontal') {
                var diffX = this.touchmove.x - touch.pageX;
                if (Math.abs(diffX) > 50) {
                    return diffX < 0 ? "prev" : "next";
                }
            } else {
                var diffY = this.touchmove.y - touch.pageY;
                if (Math.abs(diffY) > 0) {
                    return diffY > 0 ? "next" : "prev";
                }
            }
        }
        return false;
    },
    doMove: function (direction) {
        switch (direction) {
            case 'prev':
                this.slider.prev();
                break;
            case 'next':
                this.slider.next();
                break;
            default:
                break;
        }
        this.touchend = false;
        this.touchmove.x = -1;
        this.touchmove.y = -1;
        this.slider.resumeAuto();
    },
    reInit:function (){
        var windowWidth = window.innerWidth;
        this.enable = (this.element.getWidth() < 0.8 * windowWidth) ? true : false;
    }

};
function isTouchDevice() {
    var el = document.createElement('div');
    el.setAttribute('ontouchstart', 'return;');
    return typeof el.ontouchstart === "function";
}
