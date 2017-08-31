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
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * SimiPOS Product Options Model
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Api_Product_Options
{
    /**
     * Check product has options or not
     * 
     * @param type $product
     * @return boolean
     */
    public function hasOptions($product)
    {
        if (in_array($product->getTypeId(), array(
            'bundle', 'configurable', 'giftvoucher', 'grouped'
        ))) {
            return true;
        }
        if ($product->getTypeInstance(true)->hasOptions($product)) {
            return true;
        }
        return false;
    }
    
    /**
     * Retrieve all options for products
     * 
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getOptions($product)
    {
        $result = array();
        switch ($product->getTypeId()) {
            case 'bundle':
                $result = $this->getBundleOptions($product);
                break;
            case 'configurable':
                $result = $this->getConfigurableOptions($product);
                break;
            case 'giftvoucher':
                $result = $this->getGiftcardOptions($product);
                break;
            case 'giftcard':
            	$result = $this->getGiftcardEnterpriseOptions($product);
            	break;
            case 'grouped':
                $result = $this->getGroupedOptions($product);
                break;
            default :
                break;
        }
        if (!$product->getTypeInstance(true)->hasOptions($product)) {
            return $result;
        }
        // Render custom options to array
        foreach ($product->getOptions() as $option) {
            $opt = array(
                'id'    => $option->getId(),
                'type'  => $option->getType(),
                'group' => $option->getGroupByType(),
                'required'  => $option->getIsRequire(),
                'title' => $option->getTitle(),
                'name'  => 'options[' . $option->getId() . ']',
            );
            if ($option->getGroupByType() == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
                $opt['values'] = array();
                foreach ($option->getValues() as $value) {
                    $opt['values'][$value->getId()] = array(
                        'id'    => $value->getId(),
                        'title' => $value->getTitle(),
                    );
                }
            } else {
                $opt['max_chars'] = $option->getMaxCharacters();
            }
            $result[] = $opt;
        }
        return $result;
    }
    
/*******************************************************************************
 ************************ Custom Option for Product Types **********************
 ******************************************************************************/
    public function getBundleOptions($product)
    {
        $typeInstance = $product->getTypeInstance(true);
        $typeInstance->setStoreFilter($product->getStoreId(), $product);
        $optionCollection = $typeInstance->getOptionsCollection($product);
        $selectionCollection = $typeInstance->getSelectionsCollection(
            $typeInstance->getOptionsIds($product),
            $product
        );
        $options = $optionCollection->appendSelections($selectionCollection, false, false);
        
        $result = array();
        foreach ($options as $option) {
            if (!$option->getSelections()) {
                continue;
            }
            $opt = array(
                'id'    => $option->getId(),
                'type'  => $option->getType(),
                'group' => 'select',
                'required'  => $option->getRequired(),
                'title' => $option->getTitle(),
                'name'  => 'bundle_option[' . $option->getId() . ']',
                'values'    => array(),
            );
            foreach ($option->getSelections() as $selection) {
                $opt['values'][$selection->getSelectionId()] = array(
                    'id'    => $selection->getSelectionId(),
                    'title' => $selection->getName(),
                );
            }
            $result[] = $opt;
        }
        return $result;
    }
    
    public function getConfigurableOptions($product)
    {
        $attributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);
        $options = array();
        $stocks = array();
        foreach ($product->getTypeInstance(true)->getUsedProducts(null, $product) as $_product) {
            foreach ($attributes as $_attribute) {
                $productAttribute = $_attribute->getProductAttribute();
                $productAttributeId = $productAttribute->getId();
                $attributeValue = $_product->getData($productAttribute->getAttributeCode());
                $options[$productAttributeId][$attributeValue][$_product->getId()] = $_product->getId();
            }
            if (is_null($_product->getData('is_salable'))) {
            	$_product->unsetData('is_salable');
            }
            $stocks[$_product->getId()] = $_product->isSalable();
        }
        if (!count($options)) {
            return array();
        }
        $result = array();
        foreach ($attributes as $_attribute) {
            $productAttribute = $_attribute->getProductAttribute();
            $productAttributeId = $productAttribute->getId();
            $opt = array(
                'id'    => $productAttributeId,
                'type'  => 'select',
                'group' => 'select',
                'required'  => 1,
                'title' => $_attribute->getLabel(),
                'name'  => 'super_attribute[' . $productAttributeId . ']',
                'config'=> 1,
                'values'=> array(),
            );
            $prices = $_attribute->getPrices();
            if (is_array($prices)) {
                foreach ($prices as $value) {
                    if (isset($options[$productAttributeId][$value['value_index']])) {
                        $opt['values'][$value['value_index']] = array(
                            'id'    => $value['value_index'],
                            'title' => $value['label'],
                            'products'  => $options[$productAttributeId][$value['value_index']],
                        );
                        $outOfStock = true;
                        foreach ($opt['values'][$value['value_index']]['products'] as $pId) {
                        	if (!empty($stocks[$pId])) {
                        		$outOfStock = false;
                        		break;
                        	}
                        }
                        if ($outOfStock) {
                        	$opt['values'][$value['value_index']]['out_of_stock'] = true;
                        }
                    }
                }
            }
            $result[] = $opt;
        }
        return $result;
    }
    
    public function getGroupedOptions($product)
    {
        $result = array();
        foreach ($product->getTypeInstance(true)->getAssociatedProducts($product) as $item) {
            $opt = array(
                'id'    => $item->getId(),
                'type'  => 'field',
                'group' => 'text',
                'required'  => 0,
                'title' => $item->getName(),
                'name'  => 'super_group[' . $item->getId() . ']',
            );
            $result[] = $opt;
        }
        return $result;
    }
    
    public function getGiftcardOptions($product)
    {
        if (!Mage::getConfig()->getNode('global/helpers/giftvoucher/class')) {
            return array();
        }
        $labels = Mage::helper('giftvoucher')->getFullGiftVoucherOptions();
        return array(
            array(
                'id'    => "1",
                'type'  => 'checkbox',
                'group' => 'select',
                'required'  => 0,
                'title' => $labels['send_friend'],
                'name'  => 'send_friend',
                'values'=> array(
                    '1' => array(
                        'id'    => '1',
                        'title' => ''
                    )
                )
            ), array(
                'id'    => "2",
                'type'  => 'field',
                'group' => 'text',
                'required'  => 0,
                'title' => $labels['recipient_name'],
                'name'  => 'recipient_name',
            ), array(
                'id'    => "3",
                'type'  => 'field',
                'group' => 'text',
                'required'  => 0,
                'title' => $labels['recipient_email'],
                'name'  => 'recipient_email',
            ), array(
                'id'    => "4",
                'type'  => 'checkbox',
                'group' => 'select',
                'required'  => 0,
                'title' => $labels['recipient_ship'],
                'name'  => 'recipient_ship',
                'values'=> array(
                    '1' => array(
                        'id'    => '1',
                        'title' => ''
                    )
                )
            ), array(
                'id'    => "5",
                'type'  => 'area',
                'group' => 'text',
                'required'  => 0,
                'title' => $labels['message'],
                'name'  => 'message',
            ), array(
                'type'  => 'date',
                'group' => 'date',
                'required'  => 0,
                'title' => $labels['day_to_send'],
                'name'  => 'day_to_send',
            )
        );
    }
    
    public function getGiftcardEnterpriseOptions($product)
    {
        if (!Mage::getConfig()->getNode('global/helpers/enterprise_giftcard/class')) {
            return array();
        }
        $result = array();
        $store = Mage::app()->getStore($product->getStoreId());
        // Gift Card Amount
        if ($product->getGiftcardAmounts() && count($product->getGiftcardAmounts()) > 1) {
        	$values = array();
        	foreach ($product->getGiftcardAmounts() as $amount) {
        		$amount = $store->roundPrice($amount['website_value']);
        		$values["$amount"] = array(
        		    'id'      => "$amount",
        		    'title'   => $store->formatPrice($amount, false),
        		);
        	}
        	$result[] = array(
        	    'id'    => "1",
        	    'type'  => 'select',
                'group' => 'select',
                'required'  => 1,
                'title' => Mage::helper('enterprise_giftcard')->__('Amount'),
                'name'  => 'giftcard_amount',
        	    'values'=> $values
        	);
        } elseif ($product->getAllowOpenAmount()) {
        	$result[] = array(
        	    'id'    => "1",
                'type'  => 'field',
                'group' => 'text',
                'required'  => $product->getGiftcardAmounts() ? 0 : 1,
                'title' => Mage::helper('enterprise_giftcard')->__('Amount'),
        	    'name' => 'custom_giftcard_amount',
        	);
        }
        // Sender Information
        $hasEmailOption = !$product->getTypeInstance()->isTypePhysical();
        $result[] = array(
            'id'    => "2",
            'type'  => 'field',
            'group' => 'text',
            'required'  => 1,
            'title' => Mage::helper('enterprise_giftcard')->__('Sender Name'),
            'name'  => 'giftcard_sender_name',
        );
        if ($hasEmailOption) {
        	$result[] = array(
               'id'    => "3",
               'type'  => 'field',
               'group' => 'text',
               'required'  => 1,
               'title' => Mage::helper('enterprise_giftcard')->__('Sender Email'),
               'name'  => 'giftcard_sender_email',
           );
        }
        $result[] = array(
            'id'    => "4",
            'type'  => 'field',
            'group' => 'text',
            'required'  => 1,
            'title' => Mage::helper('enterprise_giftcard')->__('Recipient Name'),
            'name'  => 'giftcard_recipient_name',
        );
        if ($hasEmailOption) {
        	$result[] = array(
               'id'    => "5",
               'type'  => 'field',
               'group' => 'text',
               'required'  => 1,
               'title' => Mage::helper('enterprise_giftcard')->__('Recipient Email'),
               'name'  => 'giftcard_recipient_email',
           );
        }
        $allowMessage = (int) $product->getAllowMessage();
        if ($product->getUseConfigAllowMessage()) {
        	$allowMessage = Mage::getStoreConfigFlag(Enterprise_GiftCard_Model_Giftcard::XML_PATH_ALLOW_MESSAGE);
        }
        if ($allowMessage) {
        	$result[] = array(
               'id'    => "6",
               'type'  => 'area',
               'group' => 'text',
               'required'  => 0,
               'title' => Mage::helper('enterprise_giftcard')->__('Message'),
               'name'  => 'giftcard_message',
           );
        }
        return $result;
    }
}
