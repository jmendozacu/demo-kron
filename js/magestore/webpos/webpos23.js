function showActiveMenu(el) {
    hideHoldedOrdersDetail();
    hideBoxLogout();
    hideDropdownCategory();
    var menu_items = $$(".menu_item");
    if (menu_items.length > 0) {
        menu_items.each(function (other_el) {
            if (other_el != el) {
                other_el.removeClassName('menuactive');
                if (other_el.id != '' && other_el.id != 'reports' && $(other_el.id + '_area')) {
                    $(other_el.id + '_area').removeClassName('showing');
                    hideContainerArea(other_el.id + '_area');
                }
                if ($(other_el.id + '_area') && other_el.hasClassName('menuactive')) {
                    $(other_el.id + '_area').removeClassName('showing');
                    hideContainerArea(other_el.id + '_area');
                }
            }
        });
    }
    el.addClassName('menuactive');
    if (el.id != '' && el.id != 'reports' && $(el.id + '_area')) {
        if ($(el.id + '_area').hasClassName('showing')) {
            hideContainerArea(el.id + '_area');
            $(el.id + '_area').removeClassName('showing');
            $('checkout').addClassName('menuactive');
            el.removeClassName('menuactive');
        } else {
            showContainerArea(el.id + '_area');
            $(el.id + '_area').addClassName('show');
            $(el.id + '_area').addClassName('showing');
            $(el.id + '_area').addClassName('showMainContainer');
            el.addClassName('menuactive');
        }
    }
}

function menuClickNew(el) {
    showActiveMenu(el);
    var menuId = el.id;
    if (menuId == 'holded_orders') {
        if (holded_order_section.showing == true) {
            $('checkout').click();
        } else {
            if (typeof cashdrawer_section == 'object' && cashdrawer_section.showing == true)
                cashdrawer_section.hideArea();
            if (typeof reports_section == 'object' && reports_section.showing == true)
                reports_section.hideArea();
            holded_order_section.showArea();
            if (isRealOffline() == true) {
                var hold_order_object = new webposHoldOrder();
                hold_order_object.fillAll();
            } else {
                reloadHoldedList();
            }
        }
    }
    if (menuId == 'cash_drawer') {
        if (cashdrawer_section.showing == true) {
            $('checkout').click();
        } else {
            if (typeof holded_order_section == 'object' && holded_order_section.showing == true)
                holded_order_section.hideArea();
            if (typeof reports_section == 'object' && reports_section.showing == true)
                reports_section.hideArea();
            cashdrawer_section.showArea();
            if (isRealOffline() == true) {
            } else {
                showTransactionList();
            }
        }
    }
    if (menuId == 'reports') {
        if (reports_section.showing == true) {
            $('checkout').click();
        } else {
            reports_section.showArea();
            if (typeof holded_order_section == 'object' && holded_order_section.showing == true)
                holded_order_section.hideArea();
            if (typeof cashdrawer_section == 'object' && cashdrawer_section.showing == true)
                cashdrawer_section.hideArea();
            if (isRealOffline() == true) {
            } else {
                refreshReportSection();
            }
        }
    }
}

function showDetailHoldedOrder(el) {
    /*
     var detail = el.down('.detail');
     if (detail && detail.hasClassName('hide') == false) {
     detail.addClassName('hide');
     } else {
     detail.removeClassName('hide');
     }
     
     var items = $$('#holded_orders_section .item');
     if (items.length > 0) {
     items.each(function(other_item) {
     if (other_item != el) {
     other_item.down('.detail').addClassName('hide');
     }
     });
     }
     */
}

function checkShowDetailIn(el) {
    /*
     var detail = el.down('.detail');
     if (detail) {
     detail.removeClassName('hide');
     }
     var items = $$('#holded_orders_section .item');
     if (items.length > 0) {
     items.each(function(other_item) {
     if (other_item != el) {
     other_item.down('.detail').addClassName('hide');
     }
     });
     }
     */
}
function checkShowDetailOut(el) {
    /*
     var detail = el.down('.detail');
     if (detail) {
     detail.addClassName('hide');
     }
     */
}

function showBeforeHoldPopup() {
    var productElements = $$('#webpos_cart .needupdate');
    if (productElements.length > 0 && isOffline() == false) {
        localSet('hold_after_save_cart', 'true');
        saveCart();
    } else {
        if ($('before_hold_popup')) {
            $('before_hold_popup').removeClassName('hide');
            if ($('webpos_dark_overlay'))
                $('webpos_dark_overlay').show();
            $D('#before_hold_popup').animate({top: '20vh'});
            if ($('hold_order_descreption'))
                $('hold_order_descreption').focus();
        }
    }
}
function hideBeforeHoldPopup() {
    if ($('before_hold_popup')) {
        if ($('webpos_dark_overlay'))
            $('webpos_dark_overlay').hide();
        $D('#before_hold_popup').animate({top: '120vh'});
    }
}

function holdOrder() {
    $('hold_order_descreption_hidden').innerHTML = $('hold_order_descreption').value;
    var customer_name = customer_email = '';
    var customerid = 0;
    var items = [];
    if (typeof defaultCustomerConfig == 'object')
        customerid = defaultCustomerConfig.customer_id;
    var customerInCart = localGet('customerInCart');
    if (typeof customerInCart == 'object' && customerInCart != null && customerInCart != '') {
        var firstname = customerInCart.firstname;
        var lastname = customerInCart.lastname;
        var telephone = customerInCart.telephone;
        customer_email = customerInCart.email;
        customerid = customerInCart.customerid;
        customer_name = firstname + ' ' + lastname;
    }
    var shipping_form = $('webpos_shipping_method_form');
    var payment_form = $('webpos_payment_method_form');
    var shipping_method = $RF(shipping_form, 'shipping_method');
    var payment_method = $RF(payment_form, 'payment[method]');
    var productElements = $$('#webpos_cart .product');
    if (productElements.length > 0) {
        productElements.each(function (productEl) {
            var product_name = productEl.down('.product_name').innerHTML;
            var qty = parseFloat(productEl.down('.number').innerHTML);
            items.push(product_name + ' | Qty: ' + qty);
        });
    }
    if (isRealOffline() == true) {
        var order_data = {};
        order_data.items = JSON.stringify(items);
        order_data.order_id = '0';
        order_data.customerid = customerid;
        order_data.shipping_method = shipping_method;
        order_data.payment_method = payment_method;
        order_data.customer_name = customer_name;
        order_data.customer_email = customer_email;
        order_data.carthtml = $('webpos_cart').innerHTML;
        order_data.full_description = $D('#hold_order_descreption_hidden').text();
        order_data.short_description = $D('#hold_order_descreption_hidden').text().substring(0, 60);
        var hold_order_object = new webposHoldOrder();
        hold_order_object.add(order_data);
        if ($('hold_order_descreption'))
            $('hold_order_descreption').value = '';
        hideBeforeHoldPopup();
        showToastMessage('danger', 'Message', hold_order_success_message);
        if ($('customer_loader'))
            $('customer_loader').click()
        return;
    } else {
        if (hoding_order == false) {
            hoding_order = true;
            showBeforeHoldAjaxloader();
            var request = new Ajax.Request(hold_order_url, {
                method: 'get',
                parameters: {shipping_method: shipping_method, comment: $D('#hold_order_descreption_hidden').text()},
                onSuccess: function (transport) {
                    if (transport.status == 200) {
                        var response = JSON.parse(transport.responseText);
                        if (response.order_id) {
                            var order_data = {};
                            order_data.items = JSON.stringify(items);
                            order_data.order_id = response.order_id;
                            order_data.customerid = customerid;
                            order_data.shipping_method = shipping_method;
                            order_data.payment_method = payment_method;
                            order_data.customer_name = customer_name;
                            order_data.customer_email = customer_email;
                            order_data.carthtml = $('webpos_cart').innerHTML;
                            order_data.full_description = $D('#hold_order_descreption_hidden').text();
                            order_data.short_description = $D('#hold_order_descreption_hidden').text().substring(0, 60);
                            var hold_order_object = new webposHoldOrder();
                            hold_order_object.add(order_data);
                            if ($('hold_order_descreption'))
                                $('hold_order_descreption').value = '';
                            hideBeforeHoldPopup();
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
                            if (response.number_item && $('total_number_item'))
                                $('total_number_item').update(response.number_item);
                            showToastMessage('success', 'Message', hold_order_success_message);
                            disableCheckout();
                            showCategory();
                            deleteCustomerJs();
                            hideHoldButton();
                            emptyCart(empty_cart_url);
                        }else{
                            if (response.errorMessage && response.errorMessage != '') {
                                showToastMessage('danger', 'Error', response.errorMessage);
                            }
                        }
                    }
                    hoding_order = false;
                },
                onComplete: function() {
                    hideBeforeHoldAjaxloader();
                    hoding_order = false;
                },
                onFailure: function () {
                    showToastMessage('danger', 'Message', hold_order_error_message);
                    hoding_order = false;
                }
            });
        }
    }
}
function previewHoldedOrder(order_id) {
    var hold_order_object = new webposHoldOrder();
    hold_order_object.previewByOrderId(order_id);
}
function deleteHoldedOrder(key) {
    var hold_order_object = new webposHoldOrder();
    hold_order_object.removeByKey(key);
    $('holded_' + key).remove();
}
function deleteHoldedOrderByOrderId(order_id) {
    var hold_order_object = new webposHoldOrder();
    hold_order_object.removeByOrderId(order_id);
}
function reloadOrder(key) {
    var hold_order_object = new webposHoldOrder();
    hold_order_object.reload(key);
}
function reloadOrderByOrderId(orderId) {
    var hold_order_object = new webposHoldOrder();
    hold_order_object.reloadByOrderId(orderId);
}
function hideHoldButton() {
    if ($D('#bt_hold_order')) {
        $D('#bt_hold_order').animate({bottom: '-100%'});
        if ($D('#bt_checkout'))
            $D('#bt_checkout').animate({width: '96%'});
        if ($D('#footer_right_overlay'))
            $D('#footer_right_overlay').css({width: '100%'});
    }
}
function showHoldButton() {
    if ($D('#bt_hold_order') && isRealOffline() == false) {
        $D('#bt_hold_order').animate({bottom: '0'});
        if ($D('#bt_checkout'))
            $D('#bt_checkout').animate({width: '66%'});
        if ($D('#footer_right_overlay'))
            $D('#footer_right_overlay').css({width: '70%'});
    }
}

function buttonHoldToUnhold(key, order_id) {
    if ($('bt_hold_order')) {
        $('bt_hold_order').innerHTML = unhold_label;
        if (key != '') {
            $('bt_hold_order').setAttribute('onclick', "deleteHoldedOrder('" + key + "')");
        } else {
            $('bt_hold_order').setAttribute('onclick', "deleteHoldedOrderByOrderId('" + order_id + "')");
        }
        $('bt_hold_order').addClassName('unhold');
    }
}
function buttonUnholdToHold() {
    if ($('bt_hold_order')) {
        $('bt_hold_order').innerHTML = hold_label;
        $('bt_hold_order').setAttribute('onclick', "showBeforeHoldPopup()");
        $('bt_hold_order').removeClassName('unhold');
    }
}

function reloadHoldedList() {
    if (reloadinng_holded_list == false) {
        reloadinng_holded_list = true;
        showHoldedListAjaxloader();
        var request = new Ajax.Request(get_holded_order_url, {
            method: 'get',
            parameters: {},
            onSuccess: function (transport) {
                if (transport.status == 200) {
                    var response = JSON.parse(transport.responseText);
                    if (response && $('holded_orders_list_grid')) {
                        $('holded_orders_list_grid').update(response);
                        if ($('holded_orders_list_grid'))
                            $('holded_orders_list_grid').scrollTop = 0;
                    }
                }
                reloadinng_holded_list = false;
            },
            onComplete: function () {
                hideHoldedListAjaxloader();
                reloadinng_holded_list = false;
                showCategory();
            },
            onFailure: function () {
                reloadinng_holded_list = false;
            }
        });
    }
}

function showHoldedListAjaxloader() {
    if ($('holded_orders_section_loader'))
        $('holded_orders_section_loader').style.display = 'block';
}
function hideHoldedListAjaxloader() {
    if ($('holded_orders_section_loader'))
        $('holded_orders_section_loader').style.display = 'none';
}

function selectTill(till_id, till_name) {
    var till_data = {till_id: till_id, till_name: till_name};
    localSet('webpos_till', till_data);
    if ($('till_info')) {
        $('till_info').innerHTML = till_name;
        $('till_info').removeClassName('hide');
    }
    if (saving_till == false && isRealOffline() == false) {
        saving_till = hasAnotherRequest = true;
        $('till_area').down('.webpos_popup_loader').style.display = 'block';
        var request = new Ajax.Request(saving_till_url, {
            method: 'get',
            parameters: {till_id: till_id},
            onSuccess: function (transport) {
                if (transport.status == 200) {
                    var response = JSON.parse(transport.responseText);
                    if (response.errorMessage) {
                        showToastMessage('danger', 'Message', response.errorMessage);
                    } else {
                        hideTillSelectPopup();
                    }
                }
                saving_till = false;
            },
            onComplete: function () {
                $('till_area').down('.webpos_popup_loader').style.display = 'none';
                saving_till = hasAnotherRequest = false;
            },
            onFailure: function () {
                saving_till = hasAnotherRequest = false;
            }
        });
    }
}

function showTillSelectPopup() {
    if ($D('#till_area')) {
        $D('#till_area').animate({top: '10%'});
        if ($D('#webpos_fixed_overlay')) {
            $D('#webpos_fixed_overlay').css({display: 'block'});
        }
        var till_id = getCurrentTillId();
        if ($('till_' + till_id)) {
            var tills = $$('.till_item');
            if (tills.length > 0) {
                tills.each(function (el) {
                    el.removeClassName('selected-till');
                });
                $('till_' + till_id).addClassName('selected-till');
            }
        }
    }
}
function hideTillSelectPopup() {
    if ($D('#till_area')) {
        $D('#till_area').animate({top: '-100vh'});
        if ($D('#webpos_fixed_overlay')) {
            $D('#webpos_fixed_overlay').css({display: 'none'});
        }
    }
}

function showTransactionList() {
    if (isRealOffline() == true) {

    } else {
        showTransactionListAjaxloader();
        $D.ajax({
            url: get_transaction_grid_url,
            method: 'get',
            data: {till_id: getCurrentTillId()},
            success: function (response) {
                loadCurrentBalance();
                $D('#transaction_grid').html(response);
                hideTransactionListAjaxloader();
                resizeArea();
            }
        });
    }
}

function loadCurrentBalance() {
    if (isRealOffline() == true) {

    } else {
        showCurrentBalanceAjaxloader();
        $D.ajax({
            type: "GET",
            dataType: "json",
            data: {store_id: store_id, till_id: getCurrentTillId()},
            url: get_currentbalance_url
        }).done(function (data) {
            $D(".current_balance").text(data.msg);
            $D(".current_balance_fake").text(data.msg);
            hideCurrentBalanceAjaxloader();
        });
    }
}

function showMediumOverlay() {
    if ($('webpos_medium_overlay'))
        $('webpos_medium_overlay').style.display = 'block';
}
function hideMediumOverlay() {
    if ($('webpos_medium_overlay'))
        $('webpos_medium_overlay').style.display = 'none';
}
function showTransactionListAjaxloader() {
    if ($('cashdrawer_section_loader'))
        $('cashdrawer_section_loader').style.display = 'block';
}
function hideTransactionListAjaxloader() {
    if ($('cashdrawer_section_loader'))
        $('cashdrawer_section_loader').style.display = 'none';
}
function showCurrentBalanceAjaxloader() {
    if ($('current_balance_loader'))
        $('current_balance_loader').style.display = 'block';
}
function hideCurrentBalanceAjaxloader() {
    if ($('current_balance_loader'))
        $('current_balance_loader').style.display = 'none';
}
function showReportsAjaxloader() {
    if ($('reports_section_loader'))
        $('reports_section_loader').style.display = 'block';
}
function hideReportsAjaxloader() {
    if ($('reports_section_loader'))
        $('reports_section_loader').style.display = 'none';
}
function showBeforeHoldAjaxloader() {
    if ($('before_hold_popup_loader'))
        $('before_hold_popup_loader').style.display = 'block';
}
function hideBeforeHoldAjaxloader() {
    if ($('before_hold_popup_loader'))
        $('before_hold_popup_loader').style.display = 'none';
}
function showHoldedOrderDetailAjaxloader() {
    if ($('holded_orders_detail_loader'))
        $('holded_orders_detail_loader').style.display = 'block';
}
function hideHoldedOrderDetailAjaxloader() {
    if ($('holded_orders_detail_loader'))
        $('holded_orders_detail_loader').style.display = 'none';
}

function transactionInputAmountOnfocus() {
    $D('#transaction_note_wapper').show();
}
function transactionClearBox() {
    $D('#transaction_note').val('');
    $D('#transaction_amount').val('');
}
function transactionNoteAfterComplete() {
    $D('#transaction_note_wapper').hide();
    $D('#transaction_note').val('');
}
function transactionHideBox() {
    $D('#transaction_note_wapper').hide();
}

function newTransaction() {
    if (isRealOffline() == true) {

    } else {
        showTransactionListAjaxloader();
        $D.ajax({
            type: "GET",
            dataType: "json",
            url: new_transaction_url,
            data: {
                amount: $D("#transaction_amount").val(),
                type: $D("#transaction_type").val(),
                note: $D("#transaction_note").val(),
                till_id: getCurrentTillId()
            }
        }).done(function (data) {
            if (data.error) {
                showToastMessage('danger', 'Message', data.msg);
            } else {
                showToastMessage('success', 'Message', data.msg);
                showTransactionList();
            }
            $D('#transaction_amount').val('');
            transactionNoteAfterComplete();
            /*
             var convert_flag = $D('#set_transac_flag').val();
             if (convert_flag == 1) {
             var _url = set_transactionflag_url;
             $D.ajax({
             type: "GET",
             dataType: "json",
             url: _url,
             data: {
             store_id: store_id,
             till_id: getCurrentTillId()
             }
             }).done(function (data) {
             var transfer = $D('#transfer_val').val();
             $D("#transaction_amount").val(transfer);
             $D("#transaction_type").val('in');
             $D("#transaction_note").val('Cash Transfer');
             newTransaction();
             })
             .fail(function (data) {
             });
             $D('#set_transac_flag').val(0);
             }
             */
            hideTransactionListAjaxloader();
        })
                .fail(function (data) {
                    showToastMessage('danger', 'Message', transaction_not_saved_message);
                    hideTransactionListAjaxloader();
                });
    }
}

function getCurrentTillId() {
    var till_id = 0;
    if (enable_till == true) {
        var till_data = localGet('webpos_till');
        if (till_data != null) {
            return till_data.till_id;
        }
    }
    return till_id;
}

function loadReport(report_type) {
    if (report_type == '') {
        if ($('report_grid')) {
            $('report_grid').innerHTML = '';
        }
        if ($('reports_title'))
            $('reports_title').innerHTML = reports_label;
        return false;
    }
    if ($('report_filter'))
        $('report_filter').addClassName('hide');
    if ($('sreport_export'))
        $('sreport_export').addClassName('hide');
    var request_url = '';
    var report_name = reports_label;
    var parameters = {till_id: getCurrentTillId()};
    switch (report_type) {
        case 'x_report':
            request_url = x_report_url;
            report_name = x_report_name;
            break;
        case 'z_report':
            request_url = z_report_url;
            report_name = z_report_name;
            break;
        case 'eod_report':
            request_url = eod_report_url;
            report_name = eod_report_name;
            break;
        case 's_report':
            if ($('report_filter'))
                $('report_filter').removeClassName('hide');
            if ($('sreport_export'))
                $('sreport_export').removeClassName('hide');
            request_url = s_report_url;
            report_name = s_report_name;
            parameters = {'from': '', 'to': '', 'type': '', 'status': ''};
            if ($('s_report_from')) {
                parameters['from'] = $('s_report_from').value;
            }
            if ($('s_report_to')) {
                parameters['to'] = $('s_report_to').value;
            }
            if ($('s_report_status')) {
                parameters['status'] = $D('#s_report_status').val();
            }
            if ($('s_report_type')) {
                parameters['type'] = $('s_report_type').value;
            }
            break;
    }

    if (request_url != '') {
        if (isRealOffline() == true) {

        } else {
            if ($('reports_title'))
                $('reports_title').innerHTML = report_name;
            showReportsAjaxloader();
            $D.ajax({
                url: request_url,
                method: 'get',
                data: parameters,
                success: function (response) {
                    $D('#report_grid').html(response);
                    hideReportsAjaxloader();
                    if ($D('#btn_clear'))
                        $D('#btn_clear').click();
                    resizeArea();
                    if ($D('#btn_clear')) {
                        $D('#btn_clear').click();
                    }
                    if ($('s_report_from')) {
                        $D('#s_report_from').datetimepicker();
                    }
                    if ($('s_report_to')) {
                        $D('#s_report_to').datetimepicker();
                    }
                }
            });
        }
    }
}

function refreshReportSection() {
    if ($('report_type')) {
        $selected_report = $('report_type').value;
        if ($selected_report != '') {
            loadReport($selected_report);
        } else {
            if ($('report_grid')) {
                $('report_grid').innerHTML = '';
            }
            if ($('reports_title'))
                $('reports_title').innerHTML = reports_label;
        }
    }
}
function isInt(n) {
    return n % 1 === 0;
}
function isNumber(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
}

function calTotalManual(diff_value) {
    var sum = 0;
    if ($D('#other_payment').val() != "" && $D('#checkmo').val() != "" && $D('#cashpayment').val() != "") {
        var other_payment = parseFloat($D('#other_payment').val());
        var checkmo = parseFloat($D('#checkmo').val());
        var cash = parseFloat($D('#cashpayment').val());
        var ccsave = parseFloat($D('#ccsave').val());
        if (isNumber(other_payment))
            sum += other_payment;
        if (isNumber(checkmo))
            sum += checkmo;
        if (isNumber(cash))
            sum += cash;
        if (isNumber(ccsave))
            sum += ccsave;
        if (other_payment != "NaN" && checkmo != "NaN" && cash != "NaN" && ccsave != "NaN")
            $D('#total_value_report').html(sum.toFixed(2));
        var other_payment_diff = parseFloat($D('#other_payment_diff').text());
        var checkmo_diff = parseFloat($D('#checkmo_diff').text());
        var cashpayment_diff = parseFloat($D('#cashpayment_diff').text());
        var ccsave_diff = parseFloat($D('#ccsave_diff').text());
        var sum1 = 0;
        if (isNumber(other_payment_diff))
            sum1 += other_payment_diff;
        if (isNumber(checkmo_diff))
            sum1 += checkmo_diff;
        if (isNumber(cashpayment_diff))
            sum1 += cashpayment_diff;
        if (isNumber(ccsave_diff))
            sum1 += ccsave_diff;
        $D('#total_value_diff').html(sum1.toFixed(2));
    }

    $D('#total_value_diff').html(diff_value);
}

function cashCounted(el) {
    var class_element = el.getAttribute('class');
    var text = el.value;
    if (text != "") {
        var number = parseFloat(text);
        if (isNumber(number))
            el.value = number.toFixed(2);
        if (class_element.match('payment')) {
            var name = el.getAttribute('id');
            var id_sys = name + "_money_system";
            var manual_val = parseFloat(getPriceFromString($D('#' + name).val()));
            var system_val = parseFloat(getPriceFromString($D('#' + id_sys).text()));
            var diff_value = parseFloat(manual_val - system_val).toFixed(2);
            $D('#' + name + '_diff').html(diff_value);
            calTotalManual(diff_value);
        }
    }
}

function countTotalCash() {
    var total = 0;
    $D(".sum_value").each(function () {
        var this_value = parseFloat($D(this).val());
        if (isNumber(this_value)) {
            total += this_value;
        }
    });
    $D('#total_count').val(parseFloat(total).toFixed(2));
    $D('#btn_total').html('Total ' + parseFloat(total).toFixed(2))
}

function showCountCashArea() {
    if ($D('#count_cash_area')) {
        $D('#count_cash_area').animate({top: '10vh'});
        if ($D('#webpos_fixed_overlay')) {
            $D('#webpos_fixed_overlay').css({display: 'block'});
            $('webpos_fixed_overlay').setAttribute('onclick', 'hideCountCashArea()');
        }
    }
}

function hideCountCashArea() {
    if ($D('#count_cash_area')) {
        $D('#count_cash_area').animate({top: '-100vh'});
        if ($D('#webpos_fixed_overlay')) {
            $D('#webpos_fixed_overlay').css({display: 'none'});
            $('webpos_fixed_overlay').removeAttribute('onclick');
        }
    }
}

function saveZreport() {
    var checkInput = true;
    $D(".payment1").each(function () {
        var amount_count = $D(this).val();
        if (!isNumber(amount_count)) {
            alert('Enter Count Value !');
            checkInput = false;
            return false;
        }

    });
    if (checkInput) {
        var transfer = parseFloat($D('#cash_report').val()).toFixed(2);
        if (!isNumber(transfer)) {
            alert('Enter Amount Transfer !');
            return false;
        }
        if (transfer >= 0 && isNumber(transfer)) {
            var till_current_balance = parseFloat($D('#till_current_balance').val());
            if (!isNumber(till_current_balance)) {
                till_current_balance = 0;
            }
            $D("#transaction_amount").val(till_current_balance);
            $D("#transaction_type").val('out');
            $D("#transaction_note").val('Close Store');
            $D('#set_transac_flag').val(1);
            $D('#transfer_val').val(transfer);
        } else
            transfer = 0;

        var cashier_id = $D('#userid').val();
        var tax_amount = parseFloat(getPriceFromString($D('#tax_system').text()));
        var discount_amount = parseFloat(getPriceFromString($D('#discount_system').text()));
        var cash_system = parseFloat(getPriceFromString($D('#cashforpos_money_system').text()));
        var cash_count = parseFloat($D('#cashforpos').val());
        var cc_system = parseFloat(0);
        var cc_count = parseFloat(0);
        var other_system = parseFloat(0);
        var other_count = parseFloat(0);
        if ($('ccforpos_money_system')) {
            cc_system = parseFloat($D('#ccforpos_money_system').text());
        }
        showReportsAjaxloader();
        if (isRealOffline() == true) {

        } else {
            $D.ajax({
                type: "GET",
                dataType: "json",
                url: new_transaction_url,
                data: {
                    amount: $D("#transaction_amount").val(),
                    type: $D("#transaction_type").val(),
                    note: $D("#transaction_note").val(),
                    till_id: getCurrentTillId()
                }
            }).done(function (data) {
                var params = {
                    order_total: $D('#num_order_total').text(),
                    amount_total: getPriceFromString($D('#grand_system').text()),
                    till_id: getCurrentTillId(),
                    cashier_id: cashier_id,
                    transfer_amount: transfer,
                    tax_amount: tax_amount,
                    discount_amount: discount_amount,
                    cash_system: cash_system,
                    cash_count: cash_count,
                    cc_system: cc_system,
                    cc_count: cc_count,
                    other_system: other_system,
                    other_count: other_count
                };
                if (transfer > 0 && isNumber(transfer)) {
                    params['amount'] = transfer;
                    params['type'] = 'in';
                    params['note'] = 'Opening Balance in Drawer';
                }
                $D('#transaction_amount').val('');
                transactionNoteAfterComplete();
                $D.ajax({
                    type: "POST",
                    dataType: "json",
                    url: save_zreport_url,
                    data: params
                }).done(function (data) {
                    hideReportsAjaxloader();

                })
                        .fail(function (data) {
                            showToastMessage('danger', 'Message', report_not_saved_message);
                            hideReportsAjaxloader();
                        });
                var diff_total = parseFloat($D('#total_value_diff').text()).toFixed(2);
                zReportPrint(transfer, diff_total);
                $D('#checkout').trigger('click');
                if ($D('#btn_clear')) {
                    $D('#btn_clear').click();
                }
            })
        }
    }
}

function showContainerArea(containerId) {
    if ($D('#' + containerId)) {
        var width = $D(window).width();
        var newWidth = width - 100;
        $D('#' + containerId).animate({left: '100px', width: newWidth + "px"});
    }
}
function hideContainerArea(containerId) {
    if ($D('#' + containerId)) {
        $D('#' + containerId).animate({left: '-100vw'});
    }
}

function resizeArea() {
    var productListHeight = $D(window).height() - 105;
    $D('#content').css({height: productListHeight + 'px'});
    var newwidth = $D(window).width();
    if ($$('.mainContainer').length > 0) {
        $$('.mainContainer').each(function (container) {
            var container_id = container.getAttribute('id');
            $D('#' + container_id).css({width: newwidth});
        });
    }
    if ($D('#main_container')) {
        var mainWidth = newwidth - 100;
        $D('#main_container').css({width: mainWidth});
    }
    if ($D('#report_grid')) {
        var newheight = $D(window).height() - 90;
        $D('#report_grid').css({maxHeight: newheight + 'px'});
    }
    if ($D('#transaction_grid')) {
        var newheight = $D(window).height() - 300;
        $D('#transaction_grid').css({maxHeight: newheight + 'px'});
    }
}

function toogleTax() {
    showColrightAjaxloader();
    showCheckoutAjaxloader();
    var request = new Ajax.Request(toogle_tax_url, {
        method: 'get',
        parameters: {},
        onSuccess: function (transport) {
            if (transport.status == 200) {
                reloadAllBlock();
            }
        },
        onComplete: function () {

        },
        onFailure: function () {
            hideColrightAjaxloader();
            hideCheckoutAjaxloader();
        }
    });
}

function showHoldedOrdersDetail(orderId) {
    if ($D('#holded_orders_detail')) {
        var increment_id = ''
        if ($('orderlist_row_' + orderId)) {
            increment_id = $('orderlist_row_' + orderId).getAttribute('increment_id');
            $D('#holded_order_grid .active').removeClass('active');
            $('orderlist_row_' + orderId).addClassName('active');
        }
        if ($D('#holded_orders_detail .order_id'))
            $D('#holded_orders_detail .order_id').html(increment_id);
        var newwidth = $D('#total-right').width() + 1;
        $D('#holded_orders_detail').css({width: newwidth});
        $D('#holded_orders_detail').animate({right: '0px'});
        if (isRealOffline() == true) {

        } else {
            hasAnotherRequest = true;
            showHoldedOrderDetailAjaxloader();
            var request = new Ajax.Request(viewOrderUrl, {
                method: 'get',
                parameters: {order_id: orderId},
                onSuccess: function (transport) {
                    if (transport.status == 200) {
                        if ($('holded_orders_detail_content'))
                            $('holded_orders_detail_content').innerHTML = transport.responseText;
                    }
                    hideHoldedOrderDetailAjaxloader();
                    hasAnotherRequest = false;
                },
                onFailure: function (transport) {
                    hideHoldedOrderDetailAjaxloader();
                    hasAnotherRequest = false;
                }
            });
        }
    }
}

function hideHoldedOrdersDetail() {
    if ($D('#holded_orders_detail')) {
        var newwidth = $D('#total-right').width() + 1;
        $D('#holded_orders_detail').css({width: newwidth});
        $D('#holded_orders_detail').animate({right: '-100vw'});
    }
}

function addTempClass(el, classname) {
    if (el) {
        el.addClassName(classname);
    }
}
function removeTempClass(el, classname) {
    if (el) {
        el.removeClassName(classname);
    }
}

function calculateSplitRemaining(currentMethod) {
    var split_child_methods = $$('.split_child_method');
    if (split_child_methods.length > 0) {
        var currentRemaining = getPriceFromString(($D('#multipaymentforpos_need_pay_amount .price').length > 0) ? $D('#multipaymentforpos_need_pay_amount .price')[0].innerHTML : $D('#multipaymentforpos_need_pay_amount').text());
        var original_remain = currentRemaining;
        if ($('multipaymentforpos_need_pay_amount').getAttribute('original_remain') != null) {
            original_remain = convertLongNumber($('multipaymentforpos_need_pay_amount').getAttribute('original_remain'));
        } else {
            $('multipaymentforpos_need_pay_amount').setAttribute('original_remain', formatNumberWithDotCommas(currentRemaining));
        }
        var totalChildsAmount = 0;
        split_child_methods.each(function (el) {
            if (el.id != currentMethod.id) {
                var child_paid_amount = convertLongNumber(el.value);
                if (child_paid_amount >= original_remain) {
                    el.value = formatNumberWithDotCommas(original_remain);
                    totalChildsAmount += original_remain;
                } else {
                    totalChildsAmount += child_paid_amount;
                }
            }
        });
        if (currentRemaining <= 0 && totalChildsAmount >= original_remain) {
            currentMethod.value = '';
            return;
        }
        var currentMethodAmount = convertLongNumber(currentMethod.value);
        if ((totalChildsAmount + currentMethodAmount) > original_remain) {
            currentMethod.value = Math.round((original_remain - totalChildsAmount) * 100) / 100;
            currentRemaining = 0;
        } else {
            currentRemaining = original_remain - totalChildsAmount - currentMethodAmount;
            currentRemaining = Math.round((currentRemaining) * 100) / 100;
        }
        if ($D('#multipaymentforpos_need_pay_amount .price').length > 0) {
            $D('#multipaymentforpos_need_pay_amount .price')[0].innerHTML = getPriceFormatedNoHtml(currentRemaining);
        } else {
            $('multipaymentforpos_need_pay_amount').innerHTML = getPriceFormatedHtml(currentRemaining);
        }
    }
}

function calculateSplitTotalRemaining() {
    var split_child_methods = $$('.split_child_method');
    if (split_child_methods.length > 0) {
        var currentRemaining = getPriceFromString(($D('#multipaymentforpos_need_pay_amount .price').length > 0) ? $D('#multipaymentforpos_need_pay_amount .price')[0].innerHTML : $D('#multipaymentforpos_need_pay_amount').text());
        var original_remain = currentRemaining;
        if ($('multipaymentforpos_need_pay_amount').getAttribute('original_remain') != null) {
            original_remain = convertLongNumber($('multipaymentforpos_need_pay_amount').getAttribute('original_remain'));
        } else {
            $('multipaymentforpos_need_pay_amount').setAttribute('original_remain', formatNumberWithDotCommas(currentRemaining));
        }
        var totalChildsAmount = 0;
        split_child_methods.each(function (el) {
            var child_paid_amount = convertLongNumber(el.value);
            if (child_paid_amount >= original_remain) {
                el.value = formatNumberWithDotCommas(original_remain);
                totalChildsAmount += original_remain;
            } else {
                totalChildsAmount += child_paid_amount;
            }
        });
        if (currentRemaining <= 0 && totalChildsAmount >= original_remain) {
            currentMethod.value = '';
            return;
        }
        currentRemaining = original_remain - totalChildsAmount;
        currentRemaining = Math.round((currentRemaining) * 100) / 100;
        if ($D('#multipaymentforpos_need_pay_amount .price').length > 0) {
            $D('#multipaymentforpos_need_pay_amount .price')[0].innerHTML = getPriceFormatedNoHtml(currentRemaining);
        } else {
            $('multipaymentforpos_need_pay_amount').innerHTML = getPriceFormatedHtml(currentRemaining);
        }
    }
}

function showEditPaymentForm() {
    if ($('edit_order_payments')) {
        $('edit_order_payments').addClassName('hide');
    }
    if ($('save_edit_order_payments')) {
        $('save_edit_order_payments').removeClassName('hide');
    }
    if ($('webpos_edit_payment_methods_form')) {
        $('webpos_edit_payment_methods_form').removeClassName('hide');
    }
    localSet('edit_payment_method_values', getEditPaymentValues());
}

function saveEditPaymentForm(orderId) {
    if ($('edit_order_payments')) {
        $('edit_order_payments').removeClassName('hide');
    }
    if ($('save_edit_order_payments')) {
        $('save_edit_order_payments').addClassName('hide');
    }
    if ($('webpos_edit_payment_methods_form')) {
        $('webpos_edit_payment_methods_form').addClassName('hide');
    }
    var startValues = localGet('edit_payment_method_values');
    var endValues = getEditPaymentValues();
    if (compareObjectEqual(startValues, endValues) == false) {
        var payment_changed_values = getChangedValues(startValues, endValues);
        showOrderDetailAjaxloader();
        var parameters = {order_id: orderId, payment_changed_values: JSON.stringify(payment_changed_values)};
        var request = new Ajax.Request(editOrderPaymentUrl, {
            method: 'post',
            parameters: parameters,
            onSuccess: function (transport) {
                hideOrderDetailAjaxloader();
                if (transport.status == 200) {
                    localDelete('edit_payment_method_values');
                    showOrder(orderId, '');
                }
                hasAnotherRequest = false;
            },
            onFailure: function (transport) {
                hideOrderDetailAjaxloader();
                hasAnotherRequest = false;
            }
        });
    }
}

function getEditPaymentValues() {
    var method_values = {};
    if ($$('.edit_payment_method_inputs').length > 0) {
        $$('.edit_payment_method_inputs').each(function (el) {
            var method_code = el.getAttribute('method_code');
            method_values[method_code] = convertLongNumber(el.value);
        });
        localSet('edit_payment_method_values', method_values);
    }
    return method_values;
}

function compareObjectEqual(startObj, newObj) {
    for (var key in startObj) {
        if (startObj[key] != newObj[key]) {
            return false;
        }
    }
    return true;
}

function getChangedValues(startObj, newObj) {
    var diff = {};
    for (var key in startObj) {
        if (startObj[key] != newObj[key]) {
            diff[key] = newObj[key];
        }
    }
    return diff;
}

function splitPaymentFocus(focusEl) {
    if (focusEl.hasClassName('edit_payment_method_inputs')) {
        calculateEditSplitPayment(focusEl);
    } else {
        var remaining = convertLongNumber(($D('#multipaymentforpos_need_pay_amount .price').length > 0) ? $D('#multipaymentforpos_need_pay_amount .price')[0].innerHTML : $D('#multipaymentforpos_need_pay_amount').text());
        var focusValue = convertLongNumber(focusEl.value);
        if (focusValue == 0 && remaining > 0) {
            focusEl.value = formatNumberWithDotCommas(remaining);
            calculateSplitTotalRemaining();
        }
    }
    focusEl.select();
    if (use_virtual_keyboard_multipayments == true) {
        webposNumberInputFocus(focusEl);
    }
}

function calculateEditSplitPayment(focusEl) {
    var grandTotal = convertLongNumber($('order_detail_grandtotal').innerHTML);
    if ($('codforpos_ref_no_edit')) {
        var codValue = convertLongNumber($('codforpos_ref_no_edit').value);
        var currentValue = convertLongNumber(focusEl.value);
        if (currentValue == 0) {
            focusEl.value = codValue;
            $('codforpos_ref_no_edit').value = 0;
        }
        if ($$('.edit_payment_method_inputs').length > 0) {
            var totalValues = 0;
            $$('.edit_payment_method_inputs').each(function (el) {
                if (el.id != 'codforpos_ref_no_edit') {
                    totalValues += convertLongNumber(el.value);
                }
            });
            if (totalValues <= grandTotal) {
                var newCodValue = Math.round((grandTotal - totalValues) * 100) / 100;
                $('codforpos_ref_no_edit').value = convertLongNumber(newCodValue);
            }
        }
    }
}

function runJSafterUpdateSections() {
    if ($$('.webpos_swiper_input').length > 0) {
        $$('.webpos_swiper_input').each(function (el) {
            var code = el.getAttribute('code');
            initSwipe(code);
        });
    }
    if ($$('.webpos_date_picker').length > 0) {
        $$('.webpos_date_picker').each(function (el) {
            $D('#' + el.id).datepicker();
        });
    }
    if ($$('.webpos_time_picker').length > 0) {
        $$('.webpos_time_picker').each(function (el) {
            $D('#' + el.id).timepicker();
        });
    }
    if ($$('.webpos_datetime_picker').length > 0) {
        $$('.webpos_datetime_picker').each(function (el) {
            $D('#' + el.id).datetimepicker();
        });
    }
}