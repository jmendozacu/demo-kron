function bundleChangeSelection(bundleOptionElement) {
    var productId = bundleOptionElement.getAttribute('prdid');

    if (bundleOptionElement.hasClassName('radio') || bundleOptionElement.hasClassName('select')) {
        var data = bundleOptionElement.value.split('|');
        var optionQty = parseInt(data[1]);
        var canChangeQty = data[3];
        var qty_input_id = bundleOptionElement.getAttribute('qty_input_id');
        if (canChangeQty == '1') {
            $(qty_input_id).disabled = false;
        } else {
            $(qty_input_id).disabled = true;
        }
        if (optionQty == null)
            optionQty = 0;
        $(qty_input_id).value = optionQty;
    }
    updateBundleTotalsPrice(productId);
}

function updateBundleTotalsPrice(productId) {
    var optionElms = $$('.' + productId + '_bundle_values');
    if (optionElms.length > 0) {
        var price = parseFloat(convertPrice($('totals_price_' + productId).getAttribute('finalprice')));
        optionElms.each(function (el) {
            if (el.hasClassName('multiple')) {
                var options = el.options;
                for (var key in options) {
                    if (options[key].selected && options[key].value != '') {
                        var data = options[key].value.split('|');
                        var optionValue = data[0];
                        var optionQty = parseInt(data[1]);
                        var optionPrice = parseFloat(data[2]);
                        price += optionPrice * optionQty;
                    }
                }
            } else if (el.hasClassName('checkbox')) {
                if (el.checked) {
                    var data = el.value.split('|');
                    var optionValue = data[0];
                    var optionQty = parseInt(data[1]);
                    var optionPrice = parseFloat(data[2]);
                    price += optionPrice * optionQty;
                }
            } else if (el.hasClassName('select') && el.value != '') {
                var data = el.value.split('|');
                var optionValue = data[0];
                var optionQty = parseInt(data[1]);
                var optionPrice = parseFloat(data[2]);
                var canChangeQty = data[3];
                var qty_input_id = el.getAttribute('qty_input_id');
                if (canChangeQty == '1') {
                    optionQty = $(qty_input_id).value;
                }
                price += optionPrice * parseInt(optionQty);
            } else if (el.hasClassName('radio')) {
                if (el.checked) {
                    var data = el.value.split('|');
                    var optionValue = data[0];
                    var optionQty = parseInt(data[1]);
                    var optionPrice = parseFloat(data[2]);
                    var canChangeQty = data[3];
                    var qty_input_id = el.getAttribute('qty_input_id');
                    if (canChangeQty == '1') {
                        optionQty = $(qty_input_id).value;
                    }
                    price += optionPrice * parseInt(optionQty);
                }
            }
        });
        $('totals_price_' + productId).innerHTML = getPriceFormatedHtml(price);
    }
}

function getBundleSelectedOptions(productId) {
    var bundleOptions = {};
    var bundleOptionsQty = {};
    var optionElms = $$('.' + productId + '_bundle_values');
    if (optionElms.length > 0) {
        optionElms.each(function (el) {
            var optionId = el.getAttribute('optionid');
            if ($(productId + '_bundle_' + optionId + '_value_qty'))
                bundleOptionsQty[optionId] = $(productId + '_bundle_' + optionId + '_value_qty').value;
            if (el.hasClassName('multiple')) {
                var options = el.options;
                for (var key in options) {
                    if (options[key].selected && options[key].value != '') {
                        var data = options[key].value.split('|');
                        var optionValue = data[0];
                        bundleOptions[optionId] = [];
                        bundleOptions[optionId].push(optionValue);
                    }
                }
            } else if (el.hasClassName('checkbox')) {
                if (el.checked) {
                    var data = el.value.split('|');
                    var optionValue = data[0];
                    if (typeof bundleOptions[optionId] == 'undefined') {
                        bundleOptions[optionId] = [];
                    }
                    bundleOptions[optionId].push(optionValue);
                    if ($(productId + '_bundle_' + optionId + '_' + optionValue + '_value_qty')) {
                        if (typeof bundleOptionsQty[optionId] == 'undefined') {
                            bundleOptionsQty[optionId] = {};
                        }
                        bundleOptionsQty[optionId][optionValue] = $(productId + '_bundle_' + optionId + '_' + optionValue + '_value_qty').value;
                    }
                }
            } else if (el.hasClassName('select') && el.value != '') {
                var data = el.value.split('|');
                var optionValue = data[0];
                bundleOptions[optionId] = optionValue;
            } else if (el.hasClassName('radio')) {
                if (el.checked) {
                    var data = el.value.split('|');
                    var optionValue = data[0];
                    bundleOptions[optionId] = optionValue;
                }
            }
        });
    }
    return {bundleOptions: bundleOptions, bundleOptionsQty: bundleOptionsQty};
}

function optionChangeSelection(optionElement) {
    var productId = optionElement.getAttribute('prdid');
    var optionElms = $$('.' + productId + '_co_values');
    var bypassOptions = [];
    if (optionElms.length > 0) {
        var price = parseFloat(convertPrice($('totals_price_' + productId).getAttribute('finalprice')));
        optionElms.each(function (el) {
            var optionId = el.getAttribute('optionid');
            if (el.hasClassName('multiple')) {
                var options = el.options;
                for (var key in options) {
                    if (options[key].selected && options[key].value != '') {
                        var extraprice = options[key].getAttribute('extraprice');
                        if (extraprice != null && parseFloat(extraprice) > 0)
                            price += parseFloat(extraprice);
                    }
                }
            } else if (el.hasClassName('checkbox') || el.hasClassName('radio')) {
                if (el.checked) {
                    var extraprice = el.getAttribute('extraprice');
                    if (extraprice != null && parseFloat(extraprice) > 0)
                        price += parseFloat(extraprice);
                }
            } else if (el.hasClassName('type_date_time')) {
                var extraprice = el.getAttribute('extraprice');
                var year = $(productId + '_co_' + optionId + '_value_year').value;
                var month = $(productId + '_co_' + optionId + '_value_month').value;
                var day = $(productId + '_co_' + optionId + '_value_day').value;
                var hour = $(productId + '_co_' + optionId + '_value_hour').value;
                var minute = $(productId + '_co_' + optionId + '_value_minute').value;
                var daypart = $(productId + '_co_' + optionId + '_value_day_part').value;
                if (year != '' && month != '' && day != '' && hour != '' && minute != '') {
                    if (extraprice != null && parseFloat(extraprice) > 0 && bypassOptions.indexOf(optionId) < 0) {
                        price += parseFloat(extraprice);
                        bypassOptions.push(optionId);
                    }
                } else
                if (extraprice != null && parseFloat(extraprice) > 0) {
                    if (bypassOptions.indexOf(optionId) > 0) {
                        bypassOptions.pop(optionId);
                        price -= parseFloat(extraprice);
                    }
                }

            } else if (el.hasClassName('type_date')) {
                bypassOptions.push();
                var year = $(productId + '_co_' + optionId + '_value_year').value;
                var month = $(productId + '_co_' + optionId + '_value_month').value;
                var day = $(productId + '_co_' + optionId + '_value_day').value;
                if (year != '' && month != '' && day != '') {
                    var extraprice = el.getAttribute('extraprice');
                    if (extraprice != null && parseFloat(extraprice) > 0)
                        price += parseFloat(extraprice);
                }
            } else if (el.hasClassName('type_time')) {
                bypassOptions.push();
                var hour = $(productId + '_co_' + optionId + '_value_hour').value;
                var minute = $(productId + '_co_' + optionId + '_value_minute').value;
                var daypart = $(productId + '_co_' + optionId + '_value_day_part').value;
                if (hour != '' && minute != '') {
                    var extraprice = el.getAttribute('extraprice');
                    if (extraprice != null && parseFloat(extraprice) > 0)
                        price += parseFloat(extraprice);
                }
            } else if (el.hasClassName('select')) {
                var options = el.options;
                for (var key in options) {
                    if (options[key].selected && options[key].value != '') {
                        var extraprice = options[key].getAttribute('extraprice');
                        var optionPrice = parseFloat(extraprice);
                        price += optionPrice;
                    }
                }
            } else if (el.value != '') {
                var extraprice = el.getAttribute('extraprice');
                var optionPrice = parseFloat(extraprice);
                price += optionPrice;
            }

        });
        var cprice = getConfigurablePrice(productId);
        if (cprice > 0) {
            var coPrice = getCustomOptionPrice(productId);
            price = coPrice + cprice;
        }
        $('totals_price_' + productId).innerHTML = getPriceFormatedHtml(price);
    }
}

function updateConfigurablePrice(productId) {
    var conditionStr = $(productId + '_options_popup').getAttribute('price_condition');
    if (conditionStr != '' && conditionStr != '[]') {
        var conditionObj = JSON.parse(conditionStr);
        var price = 0;
        var priceHtml = $('totals_price_' + productId).down('.price').innerHTML;
        var oldPrice = $(productId + '_options_popup').getAttribute('originalPrice');
        if (oldPrice == null) {
            oldPrice = convertLongNumber(getStringPriceFromString(priceHtml));
            $(productId + '_options_popup').setAttribute('originalPrice', oldPrice);
        }
        var selectedOptions = {};
        var optionInputs = $$('.input_options_' + productId);
        if (optionInputs.length > 0) {
            optionInputs.each(function (optionInput) {
                var optionId = optionInput.getAttribute('optionId');
                if (optionInput.value != '') {
                    selectedOptions[optionId] = optionInput.value;
                }
            });
        }
        var hasInCondition = false;
        var isSaleable = '';
        /*
         Start: JS for solution 1 - update price for configurable product
         ----------------------------------------------------------------
         
         for (var key in conditionObj) {
         var thisoption = true;
         for (var optionId in selectedOptions) {
         if (conditionObj[key][optionId] == null || (conditionObj[key][optionId] != null && conditionObj[key][optionId] != selectedOptions[optionId])) {
         thisoption = false;
         }
         }
         
         if (thisoption) {
         price = conditionObj[key]['price'];
         if (typeof conditionObj[key]['is_percent'] != 'undefined') {
         priceHtml = getPriceFormatedNoHtml(parseFloat(price) + parseFloat(oldPrice));
         } else {
         priceHtml = getPriceFormatedNoHtml(price);
         }
         if (conditionObj[key]['isSaleable'] != null) {
         isSaleable = conditionObj[key]['isSaleable'];
         }
         hasInCondition = true;
         break;
         }
         }
         
         ----------------------------------------------------------------
         End: JS for solution 1 - update price for configurable product
         */

        /*
         Start: JS for solution 2 - update price for configurable product
         ----------------------------------------------------------------
         */
        var optionPrice = 0;
        for (var optionId in selectedOptions) {
            for (var key in conditionObj) {
                if (conditionObj[key][optionId] != null && conditionObj[key][optionId] == selectedOptions[optionId]) {
                    optionPrice += convertLongNumber(conditionObj[key]['price']);
                    ;
                }
            }
        }
        if (optionPrice != 0) {
            hasInCondition = true;
            priceHtml = getPriceFormatedNoHtml(parseFloat(optionPrice) + parseFloat(oldPrice));
        }
        /*
         ----------------------------------------------------------------
         End: JS for solution 2 - update price for configurable product
         */
        var donebt = $$("#" + productId + "_options_popup .product-name button");
        if (donebt.length > 0) {
            if (hasInCondition == false) {
                priceHtml = getPriceFormatedNoHtml(parseFloat(oldPrice));
            }
            if (isSaleable != '' && isSaleable == 'false') {
                donebt[0].hide();
            } else {
                donebt[0].show();
            }
        }
        var coPrice = convertLongNumber(getCustomOptionPrice(productId));
        if (coPrice > 0) {
            priceHtml = getPriceFormatedNoHtml(getConfigurablePrice(productId) + coPrice);
        }
        $('totals_price_' + productId).down('.price').innerHTML = priceHtml;
    }
}

function getConfigurablePrice(productId) {
    var conditionStr = $(productId + '_options_popup').getAttribute('price_condition');
    if (conditionStr != '' && conditionStr != '[]') {
        var conditionObj = JSON.parse(conditionStr);
        var price = 0;
        var priceHtml = $('totals_price_' + productId).down('.price').innerHTML;
        var oldPrice = $(productId + '_options_popup').getAttribute('originalPrice');
        if (oldPrice == null) {
            oldPrice = convertLongNumber(getStringPriceFromString(priceHtml));
            $(productId + '_options_popup').setAttribute('originalPrice', oldPrice);
        }
        var selectedOptions = {};
        var optionInputs = $$('.input_options_' + productId);
        if (optionInputs.length > 0) {
            optionInputs.each(function (optionInput) {
                var optionId = optionInput.getAttribute('optionId');
                if (optionInput.value != '') {
                    selectedOptions[optionId] = optionInput.value;
                }
            });
        }

        var hasInCondition = false;
        /*
         Start: JS for solution 1 - update price for configurable product
         
         for (var key in conditionObj) {
         var thisoption = true;
         for (var optionId in selectedOptions) {
         if (conditionObj[key][optionId] == null || (conditionObj[key][optionId] != null && conditionObj[key][optionId] != selectedOptions[optionId])) {
         thisoption = false;
         }
         }
         
         if (thisoption) {
         price = conditionObj[key]['price'];
         hasInCondition = true;
         break;
         }
         }
         
         End: JS for solution 1 - update price for configurable product
         */

        /*
         Start: JS for solution 2 - update price for configurable product
         */
        var optionPrice = 0;
        for (var optionId in selectedOptions) {
            for (var key in conditionObj) {
                if (conditionObj[key][optionId] != null && conditionObj[key][optionId] == selectedOptions[optionId]) {
                    optionPrice += convertLongNumber(conditionObj[key]['price']);
                }
            }
        }
        if (optionPrice != 0) {
            hasInCondition = true;
            price = parseFloat(optionPrice) + parseFloat(oldPrice);
        }
        /*
         End: JS for solution 2 - update price for configurable product
         */
        var donebt = $$("#" + productId + "_options_popup .product-name button");
        if (donebt.length > 0) {
            if (hasInCondition == false) {
                price = oldPrice;
            }
        }
        return convertLongNumber(price);
    }
}

function getCustomOptionPrice(productId) {
    var optionElms = $$('.' + productId + '_co_values');
    var bypassOptions = [];
    var price = 0;
    if (optionElms.length > 0) {
        optionElms.each(function (el) {
            var optionId = el.getAttribute('optionid');
            if (el.hasClassName('multiple')) {
                var options = el.options;
                for (var key in options) {
                    if (options[key].selected && options[key].value != '') {
                        var extraprice = options[key].getAttribute('extraprice');
                        if (extraprice != null && parseFloat(extraprice) > 0)
                            price += parseFloat(extraprice);
                    }
                }
            } else if (el.hasClassName('checkbox') || el.hasClassName('radio')) {
                if (el.checked) {
                    var extraprice = el.getAttribute('extraprice');
                    if (extraprice != null && parseFloat(extraprice) > 0)
                        price += parseFloat(extraprice);
                }
            } else if (el.hasClassName('type_date_time')) {
                var extraprice = el.getAttribute('extraprice');
                var year = $(productId + '_co_' + optionId + '_value_year').value;
                var month = $(productId + '_co_' + optionId + '_value_month').value;
                var day = $(productId + '_co_' + optionId + '_value_day').value;
                var hour = $(productId + '_co_' + optionId + '_value_hour').value;
                var minute = $(productId + '_co_' + optionId + '_value_minute').value;
                var daypart = $(productId + '_co_' + optionId + '_value_day_part').value;
                if (year != '' && month != '' && day != '' && hour != '' && minute != '') {
                    if (extraprice != null && parseFloat(extraprice) > 0 && bypassOptions.indexOf(optionId) < 0) {
                        price += parseFloat(extraprice);
                        bypassOptions.push(optionId);
                    }
                } else
                if (extraprice != null && parseFloat(extraprice) > 0) {
                    if (bypassOptions.indexOf(optionId) > 0) {
                        bypassOptions.pop(optionId);
                        price -= parseFloat(extraprice);
                    }
                }

            } else if (el.hasClassName('type_date')) {
                bypassOptions.push();
                var year = $(productId + '_co_' + optionId + '_value_year').value;
                var month = $(productId + '_co_' + optionId + '_value_month').value;
                var day = $(productId + '_co_' + optionId + '_value_day').value;
                if (year != '' && month != '' && day != '') {
                    var extraprice = el.getAttribute('extraprice');
                    if (extraprice != null && parseFloat(extraprice) > 0)
                        price += parseFloat(extraprice);
                }
            } else if (el.hasClassName('type_time')) {
                bypassOptions.push();
                var hour = $(productId + '_co_' + optionId + '_value_hour').value;
                var minute = $(productId + '_co_' + optionId + '_value_minute').value;
                var daypart = $(productId + '_co_' + optionId + '_value_day_part').value;
                if (hour != '' && minute != '') {
                    var extraprice = el.getAttribute('extraprice');
                    if (extraprice != null && parseFloat(extraprice) > 0)
                        price += parseFloat(extraprice);
                }
            } else if (el.hasClassName('select')) {
                var options = el.options;
                for (var key in options) {
                    if (options[key].selected && options[key].value != '') {
                        var extraprice = options[key].getAttribute('extraprice');
                        var optionPrice = parseFloat(extraprice);
                        price += optionPrice;
                    }
                }
            } else if (el.value != '') {
                var extraprice = el.getAttribute('extraprice');
                var optionPrice = parseFloat(extraprice);
                price += optionPrice;
            }

        });
    }
    return price;
}

function getProductTierPrice(productId, qty) {
    var tier_price_data = [];
    if ($('prd_' + productId)) {
        tier_price_data = JSON.parse($('prd_' + productId).getAttribute('tier_price_data'));
    } else {
        if (useLocalSearch == true && getProductInfo(productId) != '' && getProductInfo(productId) != null) {
            var data = getProductInfo(productId);
            tier_price_data = data.tier_price_data;
        }
    }
    if (tier_price_data.length > 0) {
        var correctPrice = maxQty = '';
        var allowRules = [];
        for (var i in tier_price_data) {
            var data = tier_price_data[i];
            if ((data.website_id == 0 || data.website_id == website_id) && (data.all_groups == '1' || data.cust_group == customer_group) && (data.price_qty <= qty)) {
                allowRules.push({'qty': data.price_qty, 'price': data.price});
            }
        }
        if (allowRules.length > 0) {
            for (var j in allowRules) {
                if (maxQty == '') {
                    maxQty = allowRules[j].qty;
                    correctPrice = allowRules[j].price;
                } else {
                    if (maxQty <= allowRules[j].qty) {
                        correctPrice = allowRules[j].price;
                        maxQty = allowRules[j].qty;
                    }
                }
            }
            if (correctPrice != '') {
                return correctPrice;
            }
        }
    }
    var editting_cart_item = $('edit_cart_item_popup').getAttribute('cart_item');
    if ($('editting_cart_item')) {
        return $('editting_cart_item').getAttribute('product_price');
    }

    var editting_cart_item = $('edit_cart_item_popup').getAttribute('cart_item');
    if ($D('#prd_' + productId + ' .prd_price .price').length > 0) {
        return convertLongNumber($D('#prd_' + productId + ' .prd_price .prd_price_special .price')[0].innerHTML);
    }
    return false;
}