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
 * SimiPOS Helper
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Check POS is allowed on store or not
     * 
     * @param mixed $store
     * @return boolean
     */
    public function isEnable($store = null)
    {
        return Mage::getStoreConfigFlag('simipos/general/enable', $store);
    }
    
    /**
     * prepare post data to corrected array
     * 
     * @param array $data
     * @return array
     */
    public function prepareData($data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = $this->prepareData($value);
            }
            if (strpos($key, '[') === false) {
                $data[$key] = $value;
            } else {
                list($masterKey, $subKey) = explode('[', trim($key, ']'));
                if (!isset($data[$masterKey]) || !is_array($data[$masterKey])) {
                    $data[$masterKey] = array();
                }
                $data[$masterKey][$subKey] = $value;
                unset($data[$key]);
            }
        }
        return $data;
    }
    
    /**
     * Get custom sales product
     * 
     * @return boolean | Mage_Catalog_Model_Product
     */
    public function getCustomSaleProduct()
    {
        $product = Mage::getModel('catalog/product');
        
        if ($productId = $product->getIdBySku('simipos-customsale')) {
            return $product->load($productId);
        }

        $entityType = $product->getResource()->getEntityType();
        $attributeSet = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter($entityType->getId())
            ->getFirstItem();

        $product->setAttributeSetId($attributeSet->getId())
            ->setTypeId('simipos')
            ->setSku('simipos-customsale')
            ->setWebsiteIds(array_keys(Mage::app()->getWebsites()))
            ->setStockData(array(
                'manage_stock'              => 0,
                'use_config_manage_stock'   => 0,
            ));
        $product->addData(array(
            'name'      => 'Custom Sale',
            'weight'    => 1,
            'status'    => 1,
            'visibility'=> 1,
            'price'     => 0,
            'description'   => 'Custom Sale for POS system',
            'short_description' => 'Custom Sale for POS system',
        ));

        if (!is_array($errors = $product->validate())) {
            try {
                $product->save();
            } catch (Exception $e) {
                return false;
            }
        }
        return $product;
    }
}
