/* 
 * Web POS by Magestore.com
 * Version 2.3
 * Updated by Daniel - 12/2015
 */
var webposHoldOrder = Class.create(
        {
            initialize: function()
            {
                this.items = JSON.parse(localGet('holded_orders'));
                if (this.items == null) {
                    this.items = {};
                }
            },
            refreshData: function()
            {
                this.items = JSON.parse(localGet('holded_orders'));
                if (this.items == null) {
                    this.items = {};
                }
            },
            add: function(order_data)
            {
                this.refreshData();
                this.items = JSON.parse(localGet('holded_orders'));
                var holded_orders = {};
                if ($D.jStorage.get('holded_orders') != null) {
                    holded_orders = JSON.parse($D.jStorage.get('holded_orders'));
                    if (holded_orders['user_' + currentUserId] != null) {
                        var orderArr = $D.map(holded_orders['user_' + currentUserId], function(value, index) {
                            return [value];
                        });
                        var numberOrderHolded = orderArr.length;
                    } else
                        numberOrderHolded = 0;
                    var newnumber = numberOrderHolded + 1;
                    if (holded_orders['user_' + currentUserId] == null)
                        holded_orders['user_' + currentUserId] = {};
                    while (holded_orders['user_' + currentUserId]['order_' + newnumber] != null) {
                        newnumber++;
                    }
                    holded_orders['user_' + currentUserId]['order_' + newnumber] = order_data;
                    $D.jStorage.set('holded_orders', JSON.stringify(holded_orders));
                    localSet('last_holded_order', 'order_' + newnumber);
                } else {
                    var holded_orders = {};
                    holded_orders['user_' + currentUserId] = {'order_0': order_data};
                    $D.jStorage.set('holded_orders', JSON.stringify(holded_orders));
                    localSet('last_holded_order', 'order_0');
                }
            },
            fillAll: function()
            {
                this.refreshData();
                if (this.items['user_' + currentUserId]) {
                    if ($('holded_orders_section') && $('holded_orders_section').down('.content')) {
                        var contentArea = $('holded_orders_section').down('.content');
                        contentArea.innerHTML = '';
                        this.items = this.items['user_' + currentUserId];
                        for (var i in this.items) {
                            contentArea.innerHTML += this.getHoldedItemHtml(i, this.items[i]);
                        }
                    }
                }
            },
            getHoldedItemHtml: function(key, item) {
                var customer_name = item.customer_name;
                var short_description = item.short_description;
                var full_description = item.full_description;
                var items = JSON.parse(item.items);
                var html = "";
                html += "\
                    <div id='holded_" + key + "' class='item' onmouseover='checkShowDetailIn(this)' onmouseout='checkShowDetailOut(this)' onclick='showDetailHoldedOrder(this)'>\
                        <div class='customer_name'>" + customer_name + "</div>\
                        <div class='short_description'>" + short_description + "</div>\
                        <div class='buttons'>\
                            <div class='bt_reload_order' onclick=\"reloadOrder('" + key + "')\">" + bt_reload_order_label + "</div>\
                            <div class='bt_delete_holded_order' onclick=\"deleteHoldedOrder('" + key + "')\">" + bt_delete_holded_order_label + "</div>\
                        </div>\
                        <div class='detail hide'>\
                            <div class='products'>\
                                <ul>";
                if (items && items.length > 0) {
                    for (var i in items) {
                        if (typeof items[i] == 'string') {
                            html += "<li>" + items[i] + "</li>";
                        }
                    }
                }
                html += "</ul>\
                            </div>\
                            <div class='full_description'>\
                                " + full_description + "\
                            </div>\
                        </div>\
                    </div>\
                ";
                return html;
            },
            removeByKey: function(key) {
                var holded_orders = {};
                if ($D.jStorage.get('holded_orders') != null) {
                    holded_orders = JSON.parse($D.jStorage.get('holded_orders'));
                    if (holded_orders['user_' + currentUserId][key] != null) {
                        var order_id = holded_orders['user_' + currentUserId][key].order_id;
                        delete  holded_orders['user_' + currentUserId][key];
                        var canceled = localGet('canceled');
                        localSet('canceled', 'false');
                        if (isRealOffline() == false && order_id != '' && canceling_holded_order == false && canceled == 'false') {
                            canceling_holded_order = true;
                            hasAnotherRequest = true;
                            if ($('bt_hold_order') && $('bt_hold_order').hasClassName('unhold')) {
                                showColrightAjaxloader();
                            } else {
                                showHoldedListAjaxloader();
                            }
                            var request = new Ajax.Request(cancel_holded_order_url, {
                                method: 'get',
                                parameters: {order_id: order_id},
                                onSuccess: function(transport) {
                                    if (transport.status == 200) {
                                        var response = JSON.parse(transport.responseText);
                                        if (response.success) {

                                        }
                                        if (response.grandTotal) {
                                            $('cashin_fullamount').innerHTML = response.grandTotal;
                                            if ($('remain_value_label'))
                                                $('remain_value_label').innerHTML = response.grandTotal;
                                            if ($('remain_value'))
                                                $('remain_value').innerHTML = response.grandTotal;
                                        }
                                        if (response.downgrandtotal)
                                            $('round_down_cashin').innerHTML = response.downgrandtotal;
                                        if (response.upgrandtotal)
                                            $('round_up_cashin').innerHTML = response.upgrandtotal;
                                        if (response.grand_total)
                                            $('webpos_subtotal_button').innerHTML = response.grand_total;
                                        if (response.payment_method && $('payment_method'))
                                            $('payment_method').update(response.payment_method);
                                        if (response.shipping_method && $('shipping_method'))
                                            $('shipping_method').update(response.shipping_method);
                                        if (response.pos_items && $('webpos_cart'))
                                            $('webpos_cart').update(response.pos_items);
                                        if (response.totals && $('pos_totals'))
                                            $('pos_totals').update(response.totals);
                                        if (response.number_item && $('total_number_item')) {
                                            $('total_number_item').update(response.number_item);

                                        }
                                        if (response.errorMessage && response.errorMessage != '') {
                                            showToastMessage('danger', 'Error', response.errorMessage);
                                        }
                                    }
                                    canceling_holded_order = false;
                                    hasAnotherRequest = false;
                                    hideColrightAjaxloader();
                                },
                                onComplete: function() {
                                    canceling_holded_order = false;
                                    hasAnotherRequest = false;
                                    hideColrightAjaxloader();
                                    hideHoldedListAjaxloader();
                                    if ($('bt_hold_order') && $('bt_hold_order').hasClassName('unhold')) {
                                        $('bt_hold_order').removeClassName('unhold');
                                        buttonUnholdToHold();
                                        disableCheckout();
                                        deleteCustomerJs();
                                        hideHoldButton();
                                        if ($('main_container').hasClassName('hideCategory'))
                                            showCategory();
                                    }
                                    showToastMessage('success', 'Message', cancel_hold_order_success_message);
                                    reloadHoldedList();
                                },
                                onFailure: function() {
                                    canceling_holded_order = false;
                                    hasAnotherRequest = false;
                                    hideColrightAjaxloader();
                                    hideHoldedListAjaxloader();
                                    showToastMessage('danger', 'Message', cancel_hold_order_error_message);
                                }
                            });
                        }
                    }
                    $D.jStorage.set('holded_orders', JSON.stringify(holded_orders));
                }
            },
            removeAll: function() {
                localDelete('holded_orders');
            },
            count: function() {
                this.refreshData();
                return this.items.length;
            },
            reload: function(key) {
                this.refreshData();
                if (this.items['user_' + currentUserId]) {
                    this.items = this.items['user_' + currentUserId];
                    if (this.items[key] != null) {
                        var order_data = this.items[key];
                        var carthtml = order_data.carthtml;
                        var shipping_method = order_data.shipping_method;
                        var payment_method = order_data.payment_method;
                        var customer_name = order_data.customer_name;
                        var customer_id = order_data.customerid;
                        var customer_email = order_data.customer_email;
                        var order_id = order_data.order_id;
                        $('webpos_cart').innerHTML = carthtml;
                        if (isRealOffline() == true) {
                            if (customer_name) {
                                $('add-customer').innerHTML = '<p>' + customer_name + "</p><span>" + customer_email + "</span>";
                                $D('#add-customer').attr('onclick', 'showAddCustomer()');
                                $D('#add-customer').addClass('active');
                                $D('#popup-customer').hide();
                                $D('.add-customer').removeClass('active');
                                $D('#remove-customer').show();
                            }
                            if (typeof holded_order_section == 'object')
                                holded_order_section.hideArea();
                            localSet('reloading_holded_key', key);
                            buttonHoldToUnhold(key, order_id);
                        } else {
                            if (reloading_order == false) {
                                if ($('showmenu_icon')) {
                                    $('showmenu_icon').click();
                                }
                                if (typeof holded_order_section == 'object')
                                    holded_order_section.hideArea();
                                reloading_order = true;
                                hasAnotherRequest = true;
                                showColrightAjaxloader();
                                var request = new Ajax.Request(reload_order_url, {
                                    method: 'post',
                                    parameters: {order_id: order_id, customer_id: customer_id, holded_key: key},
                                    onSuccess: function(transport) {
                                        if (transport.status == 200) {
                                            var response = JSON.parse(transport.responseText);
                                            if (response.errorMessage && response.errorMessage != '') {
                                                showToastMessage('danger', 'Error', response.errorMessage);
                                            }
                                            if (response.customer_name) {
                                                $('add-customer').innerHTML = '<p>' + response.customer_name + "</p><span>" + response.customer_email + "</span>";
                                                $D('#add-customer').attr('onclick', 'showAddCustomer()');
                                                $D('#add-customer').addClass('active');
                                                $D('#popup-customer').hide();
                                                $D('.add-customer').removeClass('active');
                                                $D('#remove-customer').show();
                                            }
                                        }
                                        reloading_order = false;
                                        hasAnotherRequest = false;
                                    },
                                    onComplete: function() {
                                        reloading_order = false;
                                        hasAnotherRequest = false;
                                        buttonHoldToUnhold(key, order_id);
                                        reloadAllBlock();
                                    },
                                    onFailure: function() {
                                        hideColrightAjaxloader();
                                        reloading_order = false;
                                        hasAnotherRequest = false;
                                    }
                                });
                            }
                        }
                        enableCheckout();
                    }
                }
            },
            reloadByOrderId: function(order_id) {
                $('checkout').click();
                this.refreshData();
                var key = this.getHoldedKeyByOrderId(order_id);
                if (key != '') {
                    this.reload(key);

                } else {
                    if (isRealOffline() == true) {

                    } else {
                        if (reloading_order == false) {
                            if (typeof holded_order_section == 'object')
                                holded_order_section.hideArea();
                            reloading_order = true;
                            hasAnotherRequest = true;
                            showColrightAjaxloader();
                            var request = new Ajax.Request(reload_order_url, {
                                method: 'post',
                                parameters: {order_id: order_id},
                                onSuccess: function(transport) {
                                    if (transport.status == 200) {
                                        var response = JSON.parse(transport.responseText);
                                        if (response.errorMessage && response.errorMessage != '') {
                                            showToastMessage('danger', 'Error', response.errorMessage);
                                        }
                                        if (response.customer_name) {
                                            $('add-customer').innerHTML = '<p>' + response.customer_name + "</p><span>" + response.customer_email + "</span>";
                                            $D('#add-customer').attr('onclick', 'showAddCustomer()');
                                            $D('#add-customer').addClass('active');
                                            $D('#popup-customer').hide();
                                            $D('.add-customer').removeClass('active');
                                            $D('#remove-customer').show();
                                        }
                                    }
                                    reloading_order = false;
                                    hasAnotherRequest = false;
                                },
                                onComplete: function() {
                                    reloading_order = false;
                                    hasAnotherRequest = false;
                                    buttonHoldToUnhold(key, order_id);
                                    reloadAllBlock();
                                },
                                onFailure: function() {
                                    hideColrightAjaxloader();
                                    reloading_order = false;
                                    hasAnotherRequest = false;
                                }
                            });
                        }
                    }
                    enableCheckout();
                }
            },
            removeByOrderId: function(order_id) {
                this.refreshData();
                var key = this.getHoldedKeyByOrderId(order_id);
                if (key != '') {
                    this.removeByKey(key);
                } else {
                    var canceled = localGet('canceled');
                    localSet('canceled', 'false');
                    if (isRealOffline() == false && order_id != '' && canceling_holded_order == false && canceled == 'false') {
                        canceling_holded_order = true;
                        hasAnotherRequest = true;
                        if ($('bt_hold_order') && $('bt_hold_order').hasClassName('unhold')) {
                            showColrightAjaxloader();
                        } else {
                            showHoldedListAjaxloader();
                        }
                        var request = new Ajax.Request(cancel_holded_order_url, {
                            method: 'get',
                            parameters: {order_id: order_id},
                            onSuccess: function(transport) {
                                if (transport.status == 200) {
                                    var response = JSON.parse(transport.responseText);
                                    if (response.success) {

                                    }
                                    if (response.grandTotal) {
                                        $('cashin_fullamount').innerHTML = response.grandTotal;
                                        if ($('remain_value_label'))
                                            $('remain_value_label').innerHTML = response.grandTotal;
                                        if ($('remain_value'))
                                            $('remain_value').innerHTML = response.grandTotal;
                                    }
                                    if (response.downgrandtotal)
                                        $('round_down_cashin').innerHTML = response.downgrandtotal;
                                    if (response.upgrandtotal)
                                        $('round_up_cashin').innerHTML = response.upgrandtotal;
                                    if (response.grand_total)
                                        $('webpos_subtotal_button').innerHTML = response.grand_total;
                                    if (response.payment_method && $('payment_method'))
                                        $('payment_method').update(response.payment_method);
                                    if (response.shipping_method && $('shipping_method'))
                                        $('shipping_method').update(response.shipping_method);
                                    if (response.pos_items && $('webpos_cart'))
                                        $('webpos_cart').update(response.pos_items);
                                    if (response.totals && $('pos_totals'))
                                        $('pos_totals').update(response.totals);
                                    if (response.number_item && $('total_number_item')) {
                                        $('total_number_item').update(response.number_item);

                                    }
                                    if (response.errorMessage && response.errorMessage != '') {
                                        showToastMessage('danger', 'Error', response.errorMessage);
                                    }
                                }
                                canceling_holded_order = false;
                                hasAnotherRequest = false;
                                hideColrightAjaxloader();
                            },
                            onComplete: function() {
                                canceling_holded_order = false;
                                hasAnotherRequest = false;
                                hideColrightAjaxloader();
                                hideHoldedListAjaxloader();
                                if ($('bt_hold_order') && $('bt_hold_order').hasClassName('unhold')) {
                                    $('bt_hold_order').removeClassName('unhold');
                                    buttonUnholdToHold();
                                    disableCheckout();
                                    deleteCustomerJs();
                                    hideHoldButton();
                                    if ($('main_container').hasClassName('hideCategory'))
                                        showCategory();
                                }
                                showToastMessage('success', 'Message', cancel_hold_order_success_message);
                                reloadHoldedList();
                                collectCartTotal();
                            },
                            onFailure: function() {
                                canceling_holded_order = false;
                                hasAnotherRequest = false;
                                hideColrightAjaxloader();
                                hideHoldedListAjaxloader();
                                showToastMessage('danger', 'Message', cancel_hold_order_error_message);
                            }
                        });
                    }
                }
            },
            getHoldedKeyByOrderId: function(order_id) {
                this.refreshData();
                if (this.items['user_' + currentUserId]) {
                    this.items = this.items['user_' + currentUserId];
                    if (!$D.isEmptyObject(this.items)) {
                        for (var key in this.items) {
                            if (this.items[key].order_id == order_id) {
                                return key;
                            }
                        }
                    }
                }
                return '';
            },
            previewByOrderId: function(order_id) {
                showHoldedOrdersDetail(order_id);
            }
        });

var webposArea = Class.create(
        {
            initialize: function(elementId, startX, startY, finishX, finishY)
            {
                this.elementId = elementId;
                this.startPos = {left: startX, top: startY, zIndex: '0'};
                this.finishPos = {left: finishX, top: finishY};
                this.hideArea();
            },
            showArea: function()
            {
                this.showing = true;
                if ($D('#' + this.elementId)) {
                    var newwidth = $D('#product-left').width();
                    $D('#' + this.elementId).css({zIndex: '10', width: newwidth});
                    $D('#' + this.elementId).animate(this.finishPos);
                    showMediumOverlay();
                }
            },
            hideArea: function()
            {
                this.showing = false;
                if ($D('#' + this.elementId)) {
                    $D('#' + this.elementId).animate(this.startPos);
                    hideMediumOverlay();
                }
            },
            toggleArea: function()
            {
                if (this.showing == true)
                    this.hideArea();
                else
                    this.showArea();
            },
        });
var webposNumberBoard = Class.create(
        {
            initialize: function(inputId, startX, startY)
            {
                this.inputId = inputId;
                this.elementId = 'webpos_number_board';
                this.overlayId = 'webpos_number_board_overlay';
                this.startPos = {left: startX, top: startY, display: 'none'};
                this.finishPos = {left: "50%", top: "80%", display: 'block'};
                this.assignEvent();
            },
            showBoard: function()
            {
                this.showing = true;
                if ($D('#' + this.elementId)) {
                    $D('#' + this.elementId).addClass('show');
                    $D('#' + this.elementId).animate(this.finishPos);
                    if ($(this.overlayId)) {
                        $(this.overlayId).style.display = 'block';
                    }
                    if ($D('#' + this.inputId)) {
                        $D('#' + this.inputId).css({background: '#eee'});
                    }
                }
            },
            hideBoard: function()
            {
                this.showing = false;
                if ($D('#' + this.elementId)) {
                    $D('#' + this.elementId).animate(this.startPos);
                    if ($(this.overlayId)) {
                        $(this.overlayId).style.display = 'none';
                    }
                    if ($D('#' + this.inputId)) {
                        $D('#' + this.inputId).css({background: '#fff'});
                    }
                }
            },
            toggleBoard: function()
            {
                if (this.showing == true)
                    this.hideBoard();
                else
                    this.showBoard();
            },
            assignEvent: function()
            {
                if ($D('#' + this.elementId) && $D('#' + this.inputId)) {
                    $D('#' + this.elementId).attr('currentInput', this.inputId);
                }
                var decimalSymbol = (priceFormat.decimalSymbol) ? priceFormat.decimalSymbol : '.';
                if ($D('#' + this.elementId + ' .decimalSymbol').length > 0) {
                    $D('#' + this.elementId + ' .decimalSymbol')[0].innerHTML = decimalSymbol;
                    $D('#' + this.elementId + ' .decimalSymbol')[0].setAttribute('value', decimalSymbol);
                }
                var inputId = this.inputId;
                if ($$('#' + this.elementId + ' .numberInput').length > 0) {
                    $$('#' + this.elementId + ' .numberInput').each(function(button) {
                        button.onclick = function() {
                            var inputNumber = button.getAttribute('value');
                            if ($D('#' + inputId)) {
                                var currentValue = $D('#' + inputId).val();
                                if (inputNumber) {
                                    $D('#' + inputId).val(currentValue + inputNumber);
                                    $D('#' + inputId).change();
                                }
                            }
                        };
                    });
                }
                if ($$('#' + this.elementId + ' .clear-number').length > 0) {
                    $$('#' + this.elementId + ' .clear-number')[0].onclick = function() {
                        if ($D('#' + inputId)) {
                            var currentValue = $D('#' + inputId).val();
                            if (currentValue.length > 0) {
                                $D('#' + inputId).val(currentValue.substr(0, (currentValue.length - 1)));
                                $D('#' + inputId).change();
                            }
                        }
                    };
                }
            }
        });

var webposPopup = Class.create(
        {
            initialize: function(elementId, overlayId, cssData)
            {
                this.elementId = elementId;
                this.overlayId = overlayId;
                this.cssData = cssData;
                this.prepareCssData(cssData);
                this.startPos = {display: 'none'};
                this.finishPos = {display: 'block'};
                this.assignEvent();
                var cssData = this.cssData;
                var startPos = this.startPos;
                var finishPos = this.finishPos;
                var positionAttrArr = ['startX', 'startY', 'finishX', 'finishY'];
                var allowCssAttrArr = ['marginLeft', 'marginTop', 'maxHeight', 'overflow', 'position', 'width', 'zIndex'];
                for (var cssKey in cssData) {
                    if (positionAttrArr.indexOf(cssKey) < 0 && allowCssAttrArr.indexOf(cssKey) >= 0) {
                        if ($D('#' + this.elementId)) {
                            $D('#' + this.elementId).css(cssKey, cssData[cssKey]);
                        }
                    }
                }
                if (typeof cssData['startX'] != 'undefined') {
                    startPos['left'] = cssData['startX'];
                }
                if (typeof cssData['startY'] != 'undefined') {
                    startPos['top'] = cssData['startY'];
                }
                if (typeof cssData['finishX'] != 'undefined') {
                    finishPos['left'] = cssData['finishX'];
                }
                if (typeof cssData['finishY'] != 'undefined') {
                    finishPos['top'] = cssData['finishY'];
                }
                this.startPos = startPos;
                this.finishPos = finishPos;
            },
            showBoard: function()
            {
                this.showing = true;
                if ($D('#' + this.elementId)) {
                    $D('#' + this.elementId).addClass('show');
                    $D('#' + this.elementId).animate(this.finishPos);
                    if ($(this.overlayId)) {
                        $(this.overlayId).style.display = 'block';
                    }
                }
            },
            hideBoard: function()
            {
                this.showing = false;
                if ($D('#' + this.elementId)) {
                    $D('#' + this.elementId).animate(this.startPos);
                    if ($(this.overlayId)) {
                        $(this.overlayId).style.display = 'none';
                    }
                }
            },
            toggleBoard: function()
            {
                if (this.showing == true)
                    this.hideBoard();
                else
                    this.showBoard();
            },
            prepareCssData: function(cssData) {
                if (typeof cssData['startX'] == 'undefined') {
                    cssData['startX'] = '50%';
                }
                if (typeof cssData['startY'] == 'undefined') {
                    cssData['startY'] = '100vh';
                }
                if (typeof cssData['finishX'] == 'undefined') {
                    cssData['finishX'] = '50%';
                }
                if (typeof cssData['finishY'] == 'undefined') {
                    cssData['finishY'] = '0vh';
                }
                if (typeof cssData['width'] == 'undefined') {
                    cssData['width'] = '80vw';
                }
                if (typeof cssData['marginTop'] == 'undefined') {
                    cssData['marginTop'] = '7vh';
                }
                if (typeof cssData['position'] == 'undefined') {
                    cssData['position'] = 'fixed';
                }
                if (typeof cssData['zIndex'] == 'undefined') {
                    cssData['zIndex'] = '99';
                }
                cssData['marginLeft'] = convertLongNumber(-convertLongNumber(cssData['width']) / 2) + 'vw';
                this.cssData = cssData;
                if ($$('#' + this.elementId + ' .popup_content').length > 0) {
                    if (typeof cssData['content_maxHeight'] == 'undefined') {
                        cssData['content_maxHeight'] = '70vh';
                    }
                    if (typeof cssData['content_minHeight'] == 'undefined') {
                        cssData['content_minHeight'] = '40px';
                    }
                    if (typeof cssData['content_overflow'] == 'undefined') {
                        cssData['content_overflow'] = 'auto';
                    }
                    $D('#' + this.elementId + ' .popup_content').css({maxHeight: cssData['content_maxHeight']});
                    $D('#' + this.elementId + ' .popup_content').css({minHeight: cssData['content_minHeight']});
                    $D('#' + this.elementId + ' .popup_content').css({overflow: cssData['content_overflow']});
                }
            },
            assignEvent: function()
            {

            },
            showAjaxLoader: function() {
                if ($$('#' + this.elementId + ' .webpos_popup_loader').length > 0) {
                    $D('#' + this.elementId + ' .webpos_popup_loader').css({display: 'block'});
                }
            },
            hideAjaxLoader: function() {
                if ($$('#' + this.elementId + ' .webpos_popup_loader').length > 0) {
                    $D('#' + this.elementId + ' .webpos_popup_loader').css({display: 'none'});
                }
            },
            updateContent: function(content) {
                if ($$('#' + this.elementId + ' .popup_content').length > 0) {
                    $$('#' + this.elementId + ' .popup_content')[0].innerHTML = content;
                }
            }
        });
function webposNumberInputFocus(inputEl) {
    inputEl.blur();
    var numberBoard = new webposNumberBoard(inputEl.id, '50%', '100vh');
    numberBoard.showBoard();
}

function hideNumberBoard() {
    if ($('webpos_number_board')) {
        var currentInput = $('webpos_number_board').getAttribute('currentInput');
        if (currentInput != '') {
            var numberBoard = new webposNumberBoard(currentInput, '50%', '150vh');
            numberBoard.hideBoard();
            if ($(currentInput)) {
                $(currentInput).value = convertLongNumber($(currentInput).value);
            }
        }
    }
}

function showSplitshipPopup(orderId) {
    if (orderId == 'new') {
        if ($('create_shipment')) {
            if ($('create_shipment').checked == false  || isOffline() == true || ship_entire_items == true ) {
                webposPlaceOrder(place_order_url);
                return false;
            }
        } else {
            return false;
        }
    }

    if (typeof splitShipPopup != 'undefined') {
        splitShipPopup.showBoard();
    } else {
        var splitShipPopup = new webposPopup('splitship_area', 'webpos_fixed_overlay', []);
        splitShipPopup.showBoard();
    }
    if (orderId != null && isOffline() == false) {
        splitShipPopup.showAjaxLoader();
        hasAnotherRequest = true;
        var parameters = {order_id: orderId};
        var request = new Ajax.Request(get_items_to_ship_url, {
            method: 'post',
            parameters: parameters,
            onSuccess: function(transport) {
                hideOrderDetailAjaxloader();
                if (transport.status == 200) {
                    splitShipPopup.updateContent(transport.responseText);
                }
                hasAnotherRequest = false;
            },
            onComplete: function() {
                splitShipPopup.hideAjaxLoader();
            },
            onFailure: function(transport) {
                hasAnotherRequest = false;
            }
        });
    }
}

function hideSplitshipPopup() {
    if (typeof splitShipPopup != 'undefined') {
        splitShipPopup.hideBoard();
    } else {
        var splitShipPopup = new webposPopup('splitship_area', 'webpos_fixed_overlay', []);
        splitShipPopup.hideBoard();
    }
}

function selectedItemsToShip() {
    if ($$('.item_to_ship').length > 0) {
        hideSplitshipPopup();
        var items_to_ship = {};
        $$('.item_to_ship').each(function(el) {
            var item_id = el.getAttribute('itemid');
            var qty = convertLongNumber(el.value);
            items_to_ship[item_id] = qty;
        });
        localSet('items_to_ship', JSON.stringify(items_to_ship));
        webposPlaceOrder(place_order_url);
    } else if ($$('.order_item_to_ship').length > 0) {
        var order_id = '';
        hideSplitshipPopup();
        var items_to_ship = {};
        $$('.order_item_to_ship').each(function(el) {
            var item_id = el.getAttribute('itemid');
            var qty = convertLongNumber(el.value);
            items_to_ship[item_id] = qty;
            if (order_id == '') {
                order_id = el.getAttribute('order_id');
            }
        });
        localSet('order_item_to_ship', JSON.stringify(items_to_ship));
        shipOrder(order_id);
    }
}

function checkItemQtyToShip(el, maxQty) {
    var qty = convertLongNumber(el.value);
    var maxQty = convertLongNumber(maxQty);
    if (qty > maxQty) {
        el.value = maxQty;
    }
}