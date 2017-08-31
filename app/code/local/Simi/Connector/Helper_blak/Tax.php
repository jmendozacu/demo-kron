<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	Simi
 * @package 	Simi_Connector
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

/**
 * Connector Helper
 * 
 * @category 	Simi
 * @package 	Simi_Connector
 * @author  	Simi Developer
 */
class Simi_Connector_Helper_Tax extends Mage_Core_Helper_Abstract {

    public $_product;

    public function helperCatalog() {

        return Mage::helper('connector/catalog');
    }

    public function getProductAttribute($attribute) {
        return $this->_product->getResource()->getAttribute($attribute);
    }

    public function getProductTax($_product, &$data, $is_group_detail = false) {


        $_coreHelper = Mage::helper('core');
        $_weeeHelper = Mage::helper('weee');
        $_taxHelper = Mage::helper('tax');
        /* @var $_coreHelper Mage_Core_Helper_Data */
        /* @var $_weeeHelper Mage_Weee_Helper_Data */
        /* @var $_taxHelper Mage_Tax_Helper_Data */

        $this->_product = $_product;
        $_storeId = $_product->getStoreId();
        $_store = $_product->getStore();
        $_id = $_product->getId();
        $priveV2 = array();

        if ($_product->getTypeId() == "bundle") {
            Mage::helper('connector/bundle_tax')->getProductTax($_product, $priveV2);
            $data['show_price_v2'] = $priveV2;
            return;
        }
        $_simplePricesTax = ($_taxHelper->displayPriceIncludingTax() || $_taxHelper->displayBothPrices());
        $_minimalPriceValue = $this->_product->getMinimalPrice();
        $_minimalPriceValue = $_store->roundPrice($_store->convertPrice($_minimalPriceValue));
        $_minimalPrice = $_taxHelper->getPrice($this->_product, $_minimalPriceValue, $_simplePricesTax);
        $_convertedFinalPrice = $_store->roundPrice($_store->convertPrice($this->_product->getFinalPrice()));
        $_specialPriceStoreLabel = $this->getProductAttribute('special_price')->getStoreLabel();


        if (!$this->_product->isGrouped()) {
            $_weeeTaxAmount = $_weeeHelper->getAmountForDisplay($this->_product);
            $_weeeTaxAttributes = $_weeeHelper->getProductWeeeAttributesForRenderer($this->_product, null, null, null, true);
            $_weeeTaxAmountInclTaxes = $_weeeTaxAmount;
            if ($_weeeHelper->isTaxable()) {
                $_weeeTaxAmountInclTaxes = $_weeeHelper->getAmountInclTaxes($_weeeTaxAttributes);
            }
            $_weeeTaxAmount = $_store->roundPrice($_store->convertPrice($_weeeTaxAmount));
            $_weeeTaxAmountInclTaxes = $_store->roundPrice($_store->convertPrice($_weeeTaxAmountInclTaxes));


            $_convertedPrice = $_store->roundPrice($_store->convertPrice($_product->getPrice()));
            $_price = $_taxHelper->getPrice($_product, $_convertedPrice);
            $_regularPrice = $_taxHelper->getPrice($_product, $_convertedPrice, $_simplePricesTax);
            $_finalPrice = $_taxHelper->getPrice($_product, $_convertedFinalPrice);
            $_finalPriceInclTax = $_taxHelper->getPrice($_product, $_convertedFinalPrice, true);
            $_weeeDisplayType = $_weeeHelper->getPriceDisplayType();

            //check final price

            if ($_finalPrice >= $_price) {
                if ($_taxHelper->displayBothPrices()) {
                    // $priveV2["is_show_price"] = false;
                    if ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 0)) {
                        $this->helperCatalog()->setTax($priveV2, 'Excl. Tax', $_store->convertPrice($_price + $_weeeTaxAmount, false));
                        $this->helperCatalog()->setTax($priveV2, 'Incl. Tax', $_store->convertPrice($_finalPriceInclTax + $_weeeTaxAmountInclTaxes, false));
                    } elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 1)) {
                        $priveV2["excl_tax"] = $_store->convertPrice($_price + $_weeeTaxAmount, false);
                        $priveV2["incl_tax"] = $_store->convertPrice($_finalPriceInclTax + $_weeeTaxAmountInclTaxes, false);
                        foreach ($_weeeTaxAttributes as $_weeeTaxAttribute) {
                            $wee = array();
                            $wee["name"] = $_weeeTaxAttribute->getName();
                            $wee["amount"] = $_coreHelper->currency($_weeeTaxAttribute->getAmount(), false, false);
                            $wee["cop"] = "+";
                            $this->helperCatalog()->setTax($priveV2, 'Wee', $wee);
                        }
                    } elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 4)) {
                        $this->helperCatalog()->setTax($priveV2, 'Excl. Tax', $_store->convertPrice($_price + $_weeeTaxAmount, false));
                        $this->helperCatalog()->setTax($priveV2, 'Incl. Tax', $_store->convertPrice($_finalPriceInclTax + $_weeeTaxAmountInclTaxes, false));
                        foreach ($_weeeTaxAttributes as $_weeeTaxAttribute) {
                            $wee = array();
                            $wee["name"] = $_weeeTaxAttribute->getName();
                            $wee["amount"] = $_coreHelper->currency($_weeeTaxAttribute->getAmount() + $_weeeTaxAttribute->getTaxAmount(), false, false);
                            $wee["cop"] = "+";
                            $this->helperCatalog()->setTax($priveV2, 'Wee', $wee);
                        }
                    } elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 2)) {// excl. + weee + final  
                        $this->helperCatalog()->setTax($priveV2, 'Excl. Tax', $_store->convertPrice($_price, false));
                        $this->helperCatalog()->setTax($priveV2, 'Incl. Tax', $_store->convertPrice($_finalPriceInclTax + $_weeeTaxAmountInclTaxes, false));
                        foreach ($_weeeTaxAttributes as $_weeeTaxAttribute) {
                            $wee = array();
                            $wee["name"] = $_weeeTaxAttribute->getName();
                            $wee["amount"] = $_coreHelper->currency($_weeeTaxAttribute->getAmount() + $_weeeTaxAttribute->getTaxAmount(), false, false);
                            $wee["cop"] = "/";
                            $this->helperCatalog()->setTax($priveV2, 'Wee', $wee);
                        }
                    } else {
                        if ($_finalPrice == $_price) {
                            $this->helperCatalog()->setTax($priveV2, 'Excl. Tax', $_store->convertPrice($_price, false));
                        } else {
                            $this->helperCatalog()->setTax($priveV2, 'Excl. Tax', $_store->convertPrice($_finalPrice, false));
                        }
                        $this->helperCatalog()->setTax($priveV2, 'Incl. Tax', $_store->convertPrice($_finalPriceInclTax, false));
                    }
                } else {
                    if ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, array(0, 1))) { // including
                        $weeeAmountToDisplay = $_taxHelper->displayPriceIncludingTax() ? $_weeeTaxAmountInclTaxes : $_weeeTaxAmount;
                        $this->helperCatalog()->setTax($priveV2, 'Regular Price', $_coreHelper->currency($_price + $weeeAmountToDisplay, false, false));
                        if ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 1)) {// show description
                            foreach ($_weeeTaxAttributes as $_weeeTaxAttribute) {
                                $wee = array();
                                $wee["name"] = $_weeeTaxAttribute->getName();
                                $wee["amount"] = $_coreHelper->currency($_weeeTaxAttribute->getAmount() + ($_taxHelper->displayPriceIncludingTax() ? $_weeeTaxAttribute->getTaxAmount() : 0), false, false);
                                $wee["cop"] = "+";
                                $this->helperCatalog()->setTax($priveV2, 'Wee', $wee);
                            }
                        }
                    } elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 4)) {
                        $this->helperCatalog()->setTax($priveV2, 'Regular Price', $_store->convertPrice($_price + $_weeeTaxAmount, false));
                        foreach ($_weeeTaxAttributes as $_weeeTaxAttribute) {
                            $wee = array();
                            $wee["name"] = $_weeeTaxAttribute->getName();
                            $wee["amount"] = $_coreHelper->currency($_weeeTaxAttribute->getAmount() + $_weeeTaxAttribute->getTaxAmount(), false, false);
                            $wee["cop"] = "+";
                            $this->helperCatalog()->setTax($priveV2, 'Wee', $wee);
                        }
                    } elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 2)) {
                        $this->helperCatalog()->setTax($priveV2, 'Regular Price', $_store->convertPrice($_price, false));
                        $weeeAmountToDisplay = $_taxHelper->displayPriceIncludingTax() ? $_weeeTaxAmountInclTaxes : $_weeeTaxAmount;
                        foreach ($_weeeTaxAttributes as $_weeeTaxAttribute) {
                            $wee = array();
                            $wee["name"] = $_weeeTaxAttribute->getName();
                            $wee["amount"] = $_coreHelper->currency($_weeeTaxAttribute->getAmount() + ($_taxHelper->displayPriceIncludingTax() ? $_weeeTaxAttribute->getTaxAmount() : 0), false, false);
                            $wee["cop"] = "/";
                            $this->helperCatalog()->setTax($priveV2, 'Wee', $wee);
                        }
                    } else {
                        if ($_finalPrice == $_price) {
                            $this->helperCatalog()->setTax($priveV2, 'Regular Price', $_store->convertPrice($_price, false));
                        } else {
                            $this->helperCatalog()->setTax($priveV2, 'Regular Price', $_store->convertPrice($_finalPrice, false));
                        }
                    }
                }
            } else /* if ($_finalPrice == $_price): */ {
                $_originalWeeeTaxAmount = $_weeeHelper->getOriginalAmount($_product);
                $_originalWeeeTaxAmount = $_store->roundPrice($_store->convertPrice($_originalWeeeTaxAmount));
                if ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 0)) { // including
                    // Regular Price                    
                    $this->helperCatalog()->setTax($priveV2, 'Regular Price', $_store->convertPrice($_regularPrice + $_originalWeeeTaxAmount, false));
                    //$priveV2["special_price_label"] = $_specialPriceStoreLabel;
                    if ($_taxHelper->displayBothPrices()) {
                        $special_both = array();
                        $this->helperCatalog()->setTax($special_both, 'Excl. Tax', $_store->convertPrice($_finalPrice + $_weeeTaxAmount, false));
                        $this->helperCatalog()->setTax($special_both, 'Incl. Tax', $_store->convertPrice($_finalPriceInclTax + $_weeeTaxAmountInclTaxes, false));
                        $this->helperCatalog()->setTax($priveV2, $_specialPriceStoreLabel, $special_both);
                    } else {
                        //only show $_specialPriceStoreLabel
                        $this->helperCatalog()->setTax($priveV2, $_specialPriceStoreLabel, $_store->convertPrice($_finalPrice + $_weeeTaxAmountInclTaxes, false));
                    }
                } elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 1)) { // incl. + weee                                         
                    $this->helperCatalog()->setTax($priveV2, 'Regular Price', $_store->convertPrice($_regularPrice + $_originalWeeeTaxAmount, false));
                    if ($_taxHelper->displayBothPrices()) {
                        $special_both = array();
                        $this->helperCatalog()->setTax($special_both, 'Excl. Tax', $_store->convertPrice($_finalPrice + $_weeeTaxAmount, false));
                        $this->helperCatalog()->setTax($special_both, 'Incl. Tax', $_store->convertPrice($_finalPriceInclTax + $_weeeTaxAmountInclTaxes, false));
                        $this->helperCatalog()->setTax($priveV2, $_specialPriceStoreLabel, $special_both);
                        foreach ($_weeeTaxAttributes as $_weeeTaxAttribute) {
                            $wee = array();
                            $wee["name"] = $_weeeTaxAttribute->getName();
                            $wee["amount"] = $_coreHelper->currency($_weeeTaxAttribute->getAmount(), false, false);
                            $wee["cop"] = "+";
                            $this->helperCatalog()->setTax($priveV2, 'Wee', $wee);
                        }
                    } else {
                        $this->helperCatalog()->setTax($priveV2, 'Price', $_store->convertPrice($_finalPrice + $_weeeTaxAmountInclTaxes, false));
                        foreach ($_weeeTaxAttributes as $_weeeTaxAttribute) {
                            $wee = array();
                            $wee["name"] = $_weeeTaxAttribute->getName();
                            $wee["amount"] = $_coreHelper->currency($_weeeTaxAttribute->getAmount(), false, false);
                            $wee["cop"] = "+";
                            $this->helperCatalog()->setTax($priveV2, 'Wee', $wee);
                        }
                    }
                } elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 4)) { // incl. + weee                                           
                    $this->helperCatalog()->setTax($priveV2, 'Regular Price', $_store->convertPrice($_regularPrice + $_weeeTaxAmountInclTaxes, false));
                    $special_both = array();
                    $this->helperCatalog()->setTax($special_both, 'Excl. Tax', $_store->convertPrice($_finalPrice + $_weeeTaxAmount, false));
                    $this->helperCatalog()->setTax($special_both, 'Incl. Tax', $_store->convertPrice($_finalPriceInclTax + $_weeeTaxAmountInclTaxes, false));
                    $this->helperCatalog()->setTax($priveV2, $_specialPriceStoreLabel, $special_both);
                    foreach ($_weeeTaxAttributes as $_weeeTaxAttribute) {
                        $wee = array();
                        $wee["name"] = $_weeeTaxAttribute->getName();
                        $wee["amount"] = $_coreHelper->currency($_weeeTaxAttribute->getAmount() + $_weeeTaxAttribute->getTaxAmount(), false, false);
                        $wee["cop"] = "+";
                        $this->helperCatalog()->setTax($priveV2, 'Wee', $wee);
                    }
                } elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 2)) { // excl. + weee + final                                       
                    $this->helperCatalog()->setTax($priveV2, 'Regular Price', $_store->convertPrice($_regularPrice, false));
                    $special_both = array();
                    $this->helperCatalog()->setTax($special_both, 'Excl. Tax', $_store->convertPrice($_finalPrice + $_weeeTaxAmount, false));
                    $this->helperCatalog()->setTax($special_both, 'Incl. Tax', $_store->convertPrice($_finalPriceInclTax + $_weeeTaxAmountInclTaxes, false));
                    $this->helperCatalog()->setTax($priveV2, $_specialPriceStoreLabel, $special_both);
                    foreach ($_weeeTaxAttributes as $_weeeTaxAttribute) {
                        $wee = array();
                        $wee["name"] = $_weeeTaxAttribute->getName();
                        $wee["amount"] = $_coreHelper->currency($_weeeTaxAttribute->getAmount(), false, false);
                        $wee["cop"] = "/";
                        $this->helperCatalog()->setTax($priveV2, 'Wee', $wee);
                    }
                } else { // excl.                    
                    $this->helperCatalog()->setTax($priveV2, 'Regular Price', $_store->convertPrice($_regularPrice, false));

                    if ($_taxHelper->displayBothPrices()) {
                        $special_both = array();
                        $this->helperCatalog()->setTax($special_both, 'Excl. Tax', $_store->convertPrice($_finalPrice, false));
                        $this->helperCatalog()->setTax($special_both, 'Incl. Tax', $_store->convertPrice($_finalPriceInclTax, false));
                        $this->helperCatalog()->setTax($priveV2, $_specialPriceStoreLabel, $special_both);
                    } else {
                        $this->helperCatalog()->setTax($priveV2, $_specialPriceStoreLabel, $_store->convertPrice($_finalPrice, false));
                    }
                }
            }

            if ($this->getDisplayMinimalPrice() && $_minimalPriceValue && $_minimalPriceValue < $_convertedFinalPrice) {
                $_minimalPriceDisplayValue = $_minimalPrice;
                if ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, array(0, 1, 4))) {
                    $_minimalPriceDisplayValue = $_minimalPrice + $_weeeTaxAmount;
                }
                $this->helperCatalog()->setTax($priveV2, 'As low as', $_store->convertPrice($_minimalPriceDisplayValue, false));
            }
        } else {// group
            if (!$is_group_detail) {
                $showMinPrice = $this->getDisplayMinimalPrice();
                //  $priveV2["is_show_price"] = false;
                if ($showMinPrice && $_minimalPriceValue) {
                    $_exclTax = $_taxHelper->getPrice($_product, $_minimalPriceValue);
                    $_inclTax = $_taxHelper->getPrice($_product, $_minimalPriceValue, false);
                    $price = $showMinPrice ? $_minimalPriceValue : 0;
                } else {
                    $price = $_convertedFinalPrice;
                    $_exclTax = $_taxHelper->getPrice($_product, $price);
                    $_inclTax = $_taxHelper->getPrice($_product, $price, false);
                }

                if ($price) {
                    if ($_taxHelper->displayBothPrices()) {
                        $special_both = array();
                        $this->helperCatalog()->setTax($special_both, 'Excl. Tax', $_store->convertPrice($_exclTax, false));
                        $this->helperCatalog()->setTax($special_both, 'Incl. Tax', $_store->convertPrice($_inclTax, false));
                        $this->helperCatalog()->setTax($priveV2, 'Starting at', $special_both);
                    } else {
                        $_showPrice = $_inclTax;
                        if (!$_taxHelper->displayPriceIncludingTax()) {
                            $_showPrice = $_exclTax;
                            $this->helperCatalog()->setTax($priveV2, 'Starting at', $_store->convertPrice($_showPrice, false));
                        }
                    }
                }
            } else {
                $this->helperCatalog()->setTax($priveV2, 'Regular Price', 0);
            }
        }

        $data['show_price_v2'] = $priveV2;
    }

    public function getDisplayMinimalPrice() {
        return $this->_product->getMinimalPrice();
    }

}