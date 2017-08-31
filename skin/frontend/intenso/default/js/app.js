/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    design
 * @package     rwd_default
 * @copyright   Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

// =============================================
// Primary Break Points
// =============================================

// These should be used with the bp (max-width, xx) mixin
// where a min-width is used, remember to +1 to break correctly
// If these are changed, they must also be updated in _var.scss

var bp = {
    xsmall: 479,
    small: 599,
    medium: 770,
    large: 979,
    xlarge: 1199
};

// ==============================================
// Search
// ==============================================

/**
 * Implements a custom validation style for the search form. When the form is invalidly submitted, the validation-failed
 * class gets added to the input, but the "This is a required field." text does not display
 */
Varien.searchForm.prototype.initialize = function (form, field, emptyText) {
    this.form = $(form);
    this.field = $(field);
    this.emptyText = emptyText;

    Event.observe(this.form, 'submit', this.submit.bind(this));
    Event.observe(this.field, 'change', this.change.bind(this));
    Event.observe(this.field, 'focus', this.focus.bind(this));
    Event.observe(this.field, 'blur', this.blur.bind(this));
    this.blur();
};

Varien.searchForm.prototype.submit = function (event) {
    if (this.field.value == this.emptyText || this.field.value == ''){
        Event.stop(event);
        this.field.addClassName('validation-failed');
        this.field.focus();
        return false;
    }
    return true;
};

Varien.searchForm.prototype.change = function (event) {
    if (
        this.field.value != this.emptyText
        && this.field.value != ''
        && this.field.hasClassName('validation-failed')
    ) {
        this.field.removeClassName('validation-failed');
    }
};

Varien.searchForm.prototype.blur = function (event) {
    if (this.field.hasClassName('validation-failed')) {
        this.field.removeClassName('validation-failed');
    }
};

// ==============================================
// Pointer abstraction
// ==============================================

/**
 * This class provides an easy and abstracted mechanism to determine the
 * best pointer behavior to use -- that is, is the user currently interacting
 * with their device in a touch manner, or using a mouse.
 *
 * Since devices may use either touch or mouse or both, there is no way to
 * know the user's preferred pointer type until they interact with the site.
 *
 * To accommodate this, this class provides a method and two events
 * to determine the user's preferred pointer type.
 *
 * - getPointer() returns the last used pointer type, or, if the user has
 *   not yet interacted with the site, falls back to a Modernizr test.
 *
 * - The mouse-detected event is triggered on the window object when the user
 *   is using a mouse pointer input, or has switched from touch to mouse input.
 *   It can be observed in this manner: $j(window).on('mouse-detected', function(event) { // custom code });
 *
 * - The touch-detected event is triggered on the window object when the user
 *   is using touch pointer input, or has switched from mouse to touch input.
 *   It can be observed in this manner: $j(window).on('touch-detected', function(event) { // custom code });
 */
var PointerManager = {
    MOUSE_POINTER_TYPE: 'mouse',
    TOUCH_POINTER_TYPE: 'touch',
    POINTER_EVENT_TIMEOUT_MS: 500,
    standardTouch: false,
    touchDetectionEvent: null,
    lastTouchType: null,
    pointerTimeout: null,
    pointerEventLock: false,

    getPointerEventsSupported: function() {
        return this.standardTouch;
    },

    getPointerEventsInputTypes: function() {
        if (window.navigator.pointerEnabled) { //IE 11+
            //return string values from http://msdn.microsoft.com/en-us/library/windows/apps/hh466130.aspx
            return {
                MOUSE: 'mouse',
                TOUCH: 'touch',
                PEN: 'pen'
            };
        } else if (window.navigator.msPointerEnabled) { //IE 10
            //return numeric values from http://msdn.microsoft.com/en-us/library/windows/apps/hh466130.aspx
            return {
                MOUSE:  0x00000004,
                TOUCH:  0x00000002,
                PEN:    0x00000003
            };
        } else { //other browsers don't support pointer events
            return {}; //return empty object
        }
    },

    /**
     * If called before init(), get best guess of input pointer type
     * using Modernizr test.
     * If called after init(), get current pointer in use.
     */
    getPointer: function() {
        // On iOS devices, always default to touch, as this.lastTouchType will intermittently return 'mouse' if
        // multiple touches are triggered in rapid succession in Safari on iOS
        if(Modernizr.ios) {
            return this.TOUCH_POINTER_TYPE;
        }

        if(this.lastTouchType) {
            return this.lastTouchType;
        }

        return Modernizr.touch ? this.TOUCH_POINTER_TYPE : this.MOUSE_POINTER_TYPE;
    },

    setPointerEventLock: function() {
        this.pointerEventLock = true;
    },
    clearPointerEventLock: function() {
        this.pointerEventLock = false;
    },
    setPointerEventLockTimeout: function() {
        var that = this;

        if(this.pointerTimeout) {
            clearTimeout(this.pointerTimeout);
        }

        this.setPointerEventLock();
        this.pointerTimeout = setTimeout(function() { that.clearPointerEventLock(); }, this.POINTER_EVENT_TIMEOUT_MS);
    },

    triggerMouseEvent: function(originalEvent) {
        if(this.lastTouchType == this.MOUSE_POINTER_TYPE) {
            return; //prevent duplicate events
        }

        this.lastTouchType = this.MOUSE_POINTER_TYPE;
        $j(window).trigger('mouse-detected', originalEvent);
    },
    triggerTouchEvent: function(originalEvent) {
        if(this.lastTouchType == this.TOUCH_POINTER_TYPE) {
            return; //prevent duplicate events
        }

        this.lastTouchType = this.TOUCH_POINTER_TYPE;
        $j(window).trigger('touch-detected', originalEvent);
    },

    initEnv: function() {
        if (window.navigator.pointerEnabled) {
            this.standardTouch = true;
            this.touchDetectionEvent = 'pointermove';
        } else if (window.navigator.msPointerEnabled) {
            this.standardTouch = true;
            this.touchDetectionEvent = 'MSPointerMove';
        } else {
            this.touchDetectionEvent = 'touchstart';
        }
    },

    wirePointerDetection: function() {
        var that = this;

        if(this.standardTouch) { //standard-based touch events. Wire only one event.
            //detect pointer event
            $j(window).on(this.touchDetectionEvent, function(e) {
                switch(e.originalEvent.pointerType) {
                    case that.getPointerEventsInputTypes().MOUSE:
                        that.triggerMouseEvent(e);
                        break;
                    case that.getPointerEventsInputTypes().TOUCH:
                    case that.getPointerEventsInputTypes().PEN:
                        // intentionally group pen and touch together
                        that.triggerTouchEvent(e);
                        break;
                }
            });
        } else { //non-standard touch events. Wire touch and mouse competing events.
            //detect first touch
            $j(window).on(this.touchDetectionEvent, function(e) {
                if(that.pointerEventLock) {
                    return;
                }

                that.setPointerEventLockTimeout();
                that.triggerTouchEvent(e);
            });

            //detect mouse usage
            $j(document).on('mouseover', function(e) {
                if(that.pointerEventLock) {
                    return;
                }

                that.setPointerEventLockTimeout();
                that.triggerMouseEvent(e);
            });
        }
    },

    init: function() {
        this.initEnv();
        this.wirePointerDetection();
    }
};

/**
 * This class manages the main navigation and supports infinite nested
 * menus which support touch, mouse click, and hover correctly.
 *
 * The following is the expected behavior:
 *
 * - Hover with an actual mouse should expand the menu (at any level of nesting)
 * - Click with an actual mouse will follow the link, regardless of any children
 * - Touch will follow links without children, and toggle submenus of links with children
 *
 * Caveats:
 * - According to Mozilla's documentation (https://developer.mozilla.org/en-US/docs/Web/Guide/Events/Touch_events),
 *   Firefox has disabled Apple-style touch events on desktop, so desktop devices using Firefox will not support
 *   the desired touch behavior.
 */
var MenuManager = {
    // These variables are used to detect incorrect touch / mouse event order
    mouseEnterEventObserved: false,
    touchEventOrderIncorrect: false,
    cancelNextTouch: false,

    /**
     * This class manages touch scroll detection
     */
    TouchScroll: {
        /**
         * Touch which moves the screen vertically more than
         * this many pixels will be considered a scroll.
         */
        TOUCH_SCROLL_THRESHOLD: 20,

        touchStartPosition: null,

        /**
         * Note scroll position so that scroll action can be detected later.
         * Should probably be called on touchstart (or similar) event.
         */
        reset: function() {
            this.touchStartPosition = $j(window).scrollTop();
        },

        /**
         * Determines if touch was actually a scroll. Should probably be checked
         * on touchend (or similar) event.
         * @returns {boolean}
         */
        shouldCancelTouch: function() {
            if(this.touchStartPosition == null) {
                return false;
            }

            var scroll = $j(window).scrollTop() - this.touchStartPosition;
            return Math.abs(scroll) > this.TOUCH_SCROLL_THRESHOLD;
        }
    },

    /**
     * Determines if small screen behavior should be used.
     *
     * @returns {boolean}
     */
    useSmallScreenBehavior: function() {
        return Modernizr.mq("screen and (max-width:" + bp.medium + "px)");
    },

    /**
     * Toggles a given menu item's visibility.
     * On large screens, also closes sibling and children of sibling menus.
     *
     * @param target
     */
    toggleMenuVisibility: function(target) {
        var link = $j(target);
        var li = link.closest('li');

        if(!this.useSmallScreenBehavior()) {
            // remove menu-active from siblings and children of siblings
            li.siblings()
              .removeClass('menu-active')
              .find('li')
              .removeClass('menu-active');
            //remove menu-active from children
            li.find('li.menu-active').removeClass('menu-active');
        }

        //toggle current item's active state
        li.toggleClass('menu-active');
    },

    // --------------------------------------------
    // Initialization methods
    //

    /**
     * Initialize MenuManager and wire all required events.
     * Should only be called once.
     *
     */
    init: function() {
        this.wirePointerEvents();
    },

    /**
     * This method observes an absurd number of events
     * depending on the capabilities of the current browser
     * to implement expected header navigation functionality.
     *
     * The goal is to separate interactions into four buckets:
     * - pointer enter using an actual mouse
     * - pointer leave using an actual mouse
     * - pointer down using an actual mouse
     * - pointer down using touch
     *
     * Browsers supporting PointerEvent events will use these
     * to differentiate pointer types.
     *
     * Browsers supporting Apple-style will use those events
     * along with mouseenter / mouseleave to emulate pointer events.
     */
    wirePointerEvents: function() {
        var that = this;
        var pointerTarget = $j('#nav a.has-children');
        var hoverTarget = $j('#nav li');

        if(PointerManager.getPointerEventsSupported()) {
            // pointer events supported, so observe those type of events

            var enterEvent = window.navigator.pointerEnabled ? 'pointerenter' : 'mouseenter';
            var leaveEvent = window.navigator.pointerEnabled ? 'pointerleave' : 'mouseleave';
            var fullPointerSupport = window.navigator.pointerEnabled;

            hoverTarget.on(enterEvent, function(e) {
                if(e.originalEvent.pointerType === undefined // Browsers with partial PointerEvent support don't provide pointer type
                    || e.originalEvent.pointerType == PointerManager.getPointerEventsInputTypes().MOUSE) {

                    if(fullPointerSupport) {
                        that.mouseEnterAction(e, this);
                    } else {
                        that.PartialPointerEventsSupport.mouseEnterAction(e, this);
                    }
                }
            }).on(leaveEvent, function(e) {
                if(e.originalEvent.pointerType === undefined // Browsers with partial PointerEvent support don't provide pointer type
                    || e.originalEvent.pointerType == PointerManager.getPointerEventsInputTypes().MOUSE) {

                    if(fullPointerSupport) {
                        that.mouseLeaveAction(e, this);
                    } else {
                        that.PartialPointerEventsSupport.mouseLeaveAction(e, this);
                    }
                }
            });

            if(!fullPointerSupport) {
                //click event doesn't have pointer type on it.
                //observe MSPointerDown to set pointer type for click to find later

                pointerTarget.on('MSPointerDown', function(e) {
                    $j(this).data('pointer-type', e.originalEvent.pointerType);
                });
            }

            pointerTarget.on('click', function(e) {
                var pointerType = fullPointerSupport ? e.originalEvent.pointerType : $j(this).data('pointer-type');

                if(pointerType === undefined || pointerType == PointerManager.getPointerEventsInputTypes().MOUSE) {
                    that.mouseClickAction(e, this);
                } else {
                    if(fullPointerSupport) {
                        that.touchAction(e, this);
                    } else {
                        that.PartialPointerEventsSupport.touchAction(e, this);
                    }
                }

                $j(this).removeData('pointer-type'); // clear pointer type hint from target, if any
            });
        } else {
            //pointer events not supported, use Apple-style events to simulate

            hoverTarget.on('mouseenter', function(e) {
                // Touch events should cancel this event if a touch pointer is used.
                // Record that this method has fired so that erroneous following
                // touch events (if any) can respond accordingly.
                that.mouseEnterEventObserved = true;
                that.cancelNextTouch = true;

                that.mouseEnterAction(e, this);
            }).on('mouseleave', function(e) {
                that.mouseLeaveAction(e, this);
            });

            $j(window).on('touchstart', function(e) {
                if(that.mouseEnterEventObserved) {
                    // If mouse enter observed before touch, then device touch
                    // event order is incorrect.
                    that.touchEventOrderIncorrect = true;
                    that.mouseEnterEventObserved = false; // Reset test
                }

                // Reset TouchScroll in order to detect scroll later.
                that.TouchScroll.reset();
            });

            pointerTarget.on('touchend', function(e) {
                $j(this).data('was-touch', true); // Note that element was invoked by touch pointer

                e.preventDefault(); // Prevent mouse compatibility events from firing where possible

                if(that.TouchScroll.shouldCancelTouch()) {
                    return; // Touch was a scroll -- don't do anything else
                }

                if(that.touchEventOrderIncorrect) {
                    that.PartialTouchEventsSupport.touchAction(e, this);
                } else {
                    that.touchAction(e, this);
                }
            }).on('click', function(e) {
                if($j(this).data('was-touch')) { // Event invoked after touch
                    e.preventDefault(); // Prevent following link
                    return; // Prevent other behavior
                }

                that.mouseClickAction(e, this);
            });
        }
    },

     // --------------------------------------------
     // Behavior "buckets"
     //

    /**
     * Browsers with incomplete PointerEvent support (such as IE 10)
     * require special event management. This collection of methods
     * accommodate such browsers.
     */
    PartialPointerEventsSupport: {
        /**
         * Without proper pointerenter / pointerleave / click pointerType support,
         * we have to use mouseenter events. These end up triggering
         * lots of mouseleave events that can be misleading.
         *
         * Each touch mouseenter and click event that ends up triggering
         * an undesired mouseleave increments this lock variable.
         *
         * Mouseleave events are cancelled if this variable is > 0,
         * and then the variable is decremented regardless.
         */
        mouseleaveLock: 0,

        /**
         * Handles mouse enter behavior, but if using touch,
         * toggle menus in the absence of full PointerEvent support.
         *
         * @param event
         * @param target
         */
        mouseEnterAction: function(event, target) {
            if(MenuManager.useSmallScreenBehavior()) {
                // fall back to normal method behavior
                MenuManager.mouseEnterAction(event, target);
                return;
            }

            event.stopPropagation();

            var jtarget = $j(target);
            if(!jtarget.hasClass('level0')) {
                this.mouseleaveLock = jtarget.parents('li').length + 1;
            }

            MenuManager.toggleMenuVisibility(target);
        },

        /**
         * Handles mouse leave behaivor, but obeys the mouseleaveLock
         * to allow undesired mouseleave events to be cancelled.
         *
         * @param event
         * @param target
         */
        mouseLeaveAction: function(event, target) {
            if(MenuManager.useSmallScreenBehavior()) {
                // fall back to normal method behavior
                MenuManager.mouseLeaveAction(event, target);
                return;
            }

            if(this.mouseleaveLock > 0) {
                this.mouseleaveLock--;
                return; // suppress duplicate mouseleave event after touch
            }

            $j(target).removeClass('menu-active'); //hide all menus
        },

        /**
         * Does no work on its own, but increments mouseleaveLock
         * to prevent following undesireable mouseleave events.
         *
         * @param event
         * @param target
         */
        touchAction: function(event, target) {
            if(MenuManager.useSmallScreenBehavior()) {
                // fall back to normal method behavior
                MenuManager.touchAction(event, target);
                return;
            }
            event.preventDefault(); // prevent following link
            this.mouseleaveLock++;
        }
    },

    /**
     * Browsers with incomplete Apple-style touch event support
     * (such as the legacy Android browser) sometimes fire
     * touch events out of order. In particular, mouseenter may
     * fire before the touch events. This collection of methods
     * accommodate such browsers.
     */
    PartialTouchEventsSupport: {
        /**
         * Toggles visibility of menu, unless suppressed by previous
         * out of order mouseenter event.
         *
         * @param event
         * @param target
         */
        touchAction: function(event, target) {
            if(MenuManager.cancelNextTouch) {
                // Mouseenter has already manipulated the menu.
                // Suppress this undesired touch event.
                MenuManager.cancelNextTouch = false;
                return;
            }

            MenuManager.toggleMenuVisibility(target);
        }
    },

    /**
     * On large screens, show menu.
     * On small screens, do nothing.
     *
     * @param event
     * @param target
     */
    mouseEnterAction: function(event, target) {
        if(this.useSmallScreenBehavior()) {
            return; // don't do mouse enter functionality on smaller screens
        }

        $j(target).addClass('menu-active'); //show current menu
    },

    /**
     * On large screens, hide menu.
     * On small screens, do nothing.
     *
     * @param event
     * @param target
     */
    mouseLeaveAction: function(event, target) {
        if(this.useSmallScreenBehavior()) {
            return; // don't do mouse leave functionality on smaller screens
        }

        $j(target).removeClass('menu-active'); //hide all menus
    },

    /**
     * On large screens, don't interfere so that browser will follow link.
     * On small screens, toggle menu visibility.
     *
     * @param event
     * @param target
     */
    mouseClickAction: function(event, target) {
        if(this.useSmallScreenBehavior()) {
            event.preventDefault(); //don't follow link
            this.toggleMenuVisibility(target); //instead, toggle visibility
        }
    },

    /**
     * Toggle menu visibility, and prevent event default to avoid
     * undesired, duplicate, synthetic mouse events.
     *
     * @param event
     * @param target
     */
    touchAction: function(event, target) {
        this.toggleMenuVisibility(target);

        event.preventDefault();
    }
};

// ==============================================
// jQuery Init
// ==============================================

// Use $j(document).ready() because Magento executes Prototype inline
$j(document).ready(function () {

    // ==============================================
    // Shared Vars
    // ==============================================

    // Document
    var w = $j(window);
    var d = $j(document);
    var body = $j('body');

    Modernizr.addTest('ios', function () {
        return navigator.userAgent.match(/(iPad|iPhone|iPod)/g);
    });

    //initialize pointer abstraction manager
    PointerManager.init();

    /* Wishlist Toggle Class */

    $j(".change").click(function (e) {
        $j( this ).toggleClass('active');
        e.stopPropagation();
    });

    $j(document).click(function (e) {
        if (! $j(e.target).hasClass('.change')) $j(".change").removeClass('active');
    });

    // ==============================================
    // UI Pattern - Toggle Content (tabs and accordions in one setup)
    // ==============================================
    
    $j('.toggle-content').each(function () {
        var wrapper = jQuery(this);

        var hasTabs = wrapper.hasClass('tabs');
        var hasAccordion = wrapper.hasClass('accordion');
        var startOpen = wrapper.hasClass('open');

        var dl = wrapper.children('dl:first');
        var dts = dl.children('dt');
        var panes = dl.children('dd');
        var groups = new Array(dts, panes);

        //Create a ul for tabs if necessary.
        if (hasTabs) {
            var ul = jQuery('<ul class="toggle-tabs clearfix"></ul>');
            dts.each(function () {
                var dt = jQuery(this);
                var li = jQuery('<li></li>');
                li.html(dt.html());
                ul.append(li);
            });
            ul.insertBefore(dl);
            var lis = ul.children();
            groups.push(lis);
        }

        //Add "last" classes.
        var i;
        for (i = 0; i < groups.length; i++) {
            groups[i].filter(':last').addClass('last');
        }

        function toggleClasses(clickedItem, group) {
            var index = group.index(clickedItem);
            var i;
            for (i = 0; i < groups.length; i++) {
                groups[i].removeClass('current');
                groups[i].eq(index).addClass('current');
            }
        }

        //Toggle on tab (dt) click.
        dts.on('click', function (e) {
            //They clicked the current dt to close it. Restore the wrapper to unclicked state.
            if (jQuery(this).hasClass('current') && wrapper.hasClass('accordion-open')) {
                wrapper.removeClass('accordion-open');
            } else {
                //They're clicking something new. Reflect the explicit user interaction.
                wrapper.addClass('accordion-open');
            }
            toggleClasses(jQuery(this), dts);
        });

        //Toggle on tab (li) click.
        if (hasTabs) {
            lis.on('click', function (e) {
                toggleClasses(jQuery(this), lis);
            });
            //Open the first tab.
            lis.eq(0).trigger('click');
        }

        //Open the first accordion if desired.
        if (startOpen) {
            dts.eq(0).trigger('click');
        }

    });
});
