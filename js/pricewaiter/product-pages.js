
/*
 * Copyright 2013-2015 Price Waiter, LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 *
 */

(function() {
  $(document).observe('dom:loaded', function() {
    var getSimpleProductSku;
    if (typeof PriceWaiterOptions === 'object') {
      getSimpleProductSku = function() {
        if (PriceWaiterProductType === 'configurable') {
          spConfig.settings.each(function(setting) {
            var options, productId, settingId;
            settingId = setting.id.replace('attribute', '');
            options = spConfig.config.attributes[settingId].options;
            productId = options.find(function(option) {
              return option.id === setting.value;
            }).allowedProducts[0];
            return PriceWaiter.setSKU(PriceWaiterIdToSkus[productId]);
          });
        }
        return PriceWaiter.getSKU();
      };
      PriceWaiterOptions.onButtonClick = function(PriceWaiter, platformOnButtonClick) {
        var productConfiguration, productForm;
        if (!productAddToCartForm.validator.validate()) {
          return false;
        }
        productForm = $('product_addtocart_form');
        productConfiguration = productForm.serialize();
        PriceWaiter.setMetadata('_magento_product_configuration', encodeURIComponent(productConfiguration));
        PriceWaiter.setSKU(getSimpleProductSku());
        return platformOnButtonClick();
      };
      PriceWaiterOptions.onload = function(PriceWaiter) {
        var handleBundles, handleConfigurables, handleGrouped, handleSimples, simplesInput, simplesSelect;
        simplesSelect = function(select, name) {
          select.observe('change', function() {
            PriceWaiter.setProductOption(name, select.options[select.selectedIndex].text);
          });
        };
        simplesInput = function(select, name) {
          if (select.type === 'text' || select.tagName === 'TEXTAREA') {
            select.observe('change', function() {
              PriceWaiter.setProductOption(name, select.value);
            });
          } else {
            select.observe('change', function() {
              var optionValue;
              optionValue = select.next('span').select('label')[0].innerHTML;
              optionValue = optionValue.replace(/\s*<span.*\/span>/, '');
              PriceWaiter.setProductOption(name, optionValue);
            });
          }
        };
        handleSimples = function() {
          var current, optionLabel, optionName, productCustomOptions, productForm;
          if (typeof opConfig === 'undefined') {
            return;
          }
          productForm = $('product_addtocart_form');
          if (productForm.getInputs('file').length !== 0) {
            console.log('The PriceWaiter Name Your Price Widget does not support upload file options.');
            $$('div.name-your-price-widget').each(function(pww) {
              pww.setStyle({
                display: 'none'
              });
            });
          }
          PriceWaiter.originalOpen = PriceWaiter.open;
          PriceWaiter.open = function() {
            var innerSpan, priceElement, productPrice;
            productPrice = 0;
            priceElement = document.getElementsByRegex('^product-price-');
            innerSpan = priceElement[0].select('span');
            if (typeof innerSpan[0] === 'undefined') {
              productPrice = priceElement[0].innerHTML;
            } else {
              productPrice = innerSpan[0].innerHTML;
            }
            PriceWaiter.setPrice(productPrice);
            PriceWaiter.originalOpen();
          };
          productCustomOptions = $$('.product-custom-option');
          for (current in productCustomOptions) {
            if (!isNaN(parseInt(current, 10))) {
              optionLabel = productCustomOptions[current].up('dd').previous('dt').select('label')[0];
              optionName = optionLabel.innerHTML.replace(/^<em.*\/em>/, '');
              if (optionLabel.hasClassName('required')) {
                PriceWaiter.setProductOptionRequired(optionName);
              }
              switch (productCustomOptions[current].tagName) {
                case 'SELECT':
                  simplesSelect(productCustomOptions[current], optionName);
                  break;
                case 'INPUT':
                case 'TEXTAREA':
                  simplesInput(productCustomOptions[current], optionName);
              }
            }
          }
        };
        handleConfigurables = function() {
          spConfig.settings.each(function(setting) {
            var attributeId, optionName;
            attributeId = $(setting).id;
            attributeId = attributeId.replace(/attribute/, '');
            optionName = spConfig.config.attributes[attributeId].label;
            if ($(setting).hasClassName('required-entry') && typeof PriceWaiter.setProductOptionRequired === 'function') {
              PriceWaiter.setProductOptionRequired(optionName, true);
            }
            Event.observe(setting, 'change', function(event) {
              var optionValue;
              PriceWaiter.setPrice((Number(spConfig.config.basePrice) || 0) + (Number(spConfig.reloadPrice()) || 0));
              optionValue = setting.value !== '' ? setting.options[setting.selectedIndex].innerHTML : void 0;
              if (optionValue === void 0) {
                PriceWaiter.clearProductOption(optionName);
              } else {
                PriceWaiter.setProductOption(optionName, optionValue);
              }
            });
          });
        };
        handleBundles = function() {
          var bundleElements, bundleOption, key, matched, obj, opt, rePattern, requiredOptions;
          requiredOptions = [];
          bundleElements = document.getElementsByRegex('^bundle-option-');
          rePattern = /\[(\d*)\]/;
          for (bundleOption in bundleElements) {
            if (!isNaN(parseInt(bundleOption, 10))) {
              obj = bundleElements[bundleOption];
              if (obj.hasClassName('required-entry') || obj.hasClassName('validate-one-required-by-name')) {
                matched = rePattern.exec(obj.name);
                requiredOptions.push(parseInt(matched[1], 10));
              }
            }
          }
          requiredOptions = requiredOptions.uniq();
          for (key in bundle.config.options) {
            if (requiredOptions.indexOf(parseInt(key, 10)) > -1) {
              opt = bundle.config.options[key];
              PriceWaiter.setProductOptionRequired(opt.title, true);
            }
          }
          document.observe('bundle:reload-price', function(event) {
            var bOptions, bSelected, current, currentSelected, qty, selectedValue;
            PriceWaiter.setPrice(event.memo.priceInclTax + event.memo.bundle.config.basePrice);
            bSelected = event.memo.bundle.config.selected;
            bOptions = event.memo.bundle.config.options;
            for (current in bSelected) {
              if (isNaN(current)) {
                continue;
              }
              currentSelected = bSelected[current];
              if (currentSelected.length === 0) {
                PriceWaiter.clearProductOption(bOptions[current].title);
              } else {
                qty = bOptions[current].selections[currentSelected].qty;
                selectedValue = bOptions[current].selections[currentSelected].name;
                if (qty > 1) {
                  selectedValue += ' - Quantity: ' + qty;
                }
                PriceWaiter.setProductOption(bOptions[current].title, selectedValue);
              }
            }
          });
          if (typeof bundle !== 'undefined') {
            bundle.reloadPrice();
          }
        };
        handleGrouped = function() {
          var productRows, productTable, row;
          productTable = $$('table.grouped-items-table')[0];
          productRows = productTable.select('tbody')[0];
          productRows = productRows.childElements();
          if (productRows.length > 0) {
            PriceWaiter.setProductOptionRequired('Quantity of Products', true);
          }
          for (row in productRows) {
            if (!isNaN(parseInt(row, 10))) {
              productRows[row].select('input.qty')[0].observe('change', function(event) {
                var amountToRemove, inputName, pattern, previousQuantity, productID, productName, productPrice, qty;
                qty = this.value;
                pattern = /\[(.*)\]/;
                inputName = this.name;
                productID = pattern.exec(inputName);
                productID = productID[1];
                productName = window.PriceWaiterGroupedProductInfo[productID][0];
                productPrice = window.PriceWaiterGroupedProductInfo[productID][1];
                previousQuantity = PriceWaiter.getProductOptions()[productName + ' (' + productPrice + ')'];
                amountToRemove = Number(previousQuantity * productPrice);
                if (qty > 0) {
                  PriceWaiter.setProductOption(productName + ' (' + productPrice + ')', qty);
                  PriceWaiter.setPrice(Number(PriceWaiter.getPrice()) + Number(productPrice * qty));
                } else {
                  PriceWaiter.clearProductOption(productName + ' (' + productPrice + ')');
                }
                if (previousQuantity > 0) {
                  PriceWaiter.setPrice(Number(PriceWaiter.getPrice() - amountToRemove));
                }
                if (Object.keys(PriceWaiter.getProductOptions()).length > 0) {
                  PriceWaiter.clearRequiredProductOptions();
                } else {
                  PriceWaiter.setProductOptionRequired('Quantity of Products', true);
                }
              });
            }
          }
        };
        PriceWaiter.setRegularPrice(PriceWaiterRegularPrice);
        document['getElementsByRegex'] = function(pattern) {
          var arrElements, findRecursively, re;
          arrElements = [];
          re = new RegExp(pattern);
          findRecursively = function(aNode) {
            var idx;
            if (!aNode) {
              return;
            }
            if (aNode.id !== void 0 && aNode.id.search(re) !== -1) {
              arrElements.push(aNode);
            }
            for (idx in aNode.childNodes) {
              findRecursively(aNode.childNodes[idx]);
            }
          };
          findRecursively(document);
          return arrElements;
        };
        if ($('qty') !== null) {
          $('qty').observe('change', function() {
            PriceWaiter.setQuantity($('qty').value);
          });
        }
        switch (PriceWaiterProductType) {
          case 'simple':
            handleSimples();
            break;
          case 'configurable':
            handleConfigurables();
            break;
          case 'bundle':
            handleBundles();
            break;
          case 'grouped':
            handleGrouped();
        }
      };
    }
    if (window.PriceWaiterWidgetUrl) {
      (function() {
        var pw, s;
        pw = document.createElement('script');
        pw.type = 'text/javascript';
        pw.src = window.PriceWaiterWidgetUrl;
        pw.async = true;
        s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(pw, s);
      })();
    }
  });

}).call(this);
