<?php

class Ebizmarts_BakerlooRestful_Model_Api_Stores extends Ebizmarts_BakerlooRestful_Model_Api_Api {

    public $pageSize    = 200;
    protected $_model   = "core/store";
    public $defaultSort = "name";
    public $defaultDir  = "ASC";
    protected $_since   = false;

    public function _createDataObject($id = null, $data = null) {

        $result = null;

        if(is_null($data)) {
            $store = Mage::getModel($this->_model)->load($id);
        }
        else {
            $store = $data;
        }

        if($store->getId()) {

            $website = Mage::app()->getWebsite((int)$store->getWebsiteId());

            try {
                $group   = Mage::app()->getGroup((int)$store->getGroupId());

                $storeData = array(
                    'code'             => $store->getCode(),
                    'currency_id'      => (string)$store->getConfig('currency/options/default'),
                    'base_currency_id' => (string)$store->getConfig('currency/options/base'),
                    'email'            => (string)$store->getConfig('trans_email/ident_general/email'),
                    'group_id'         => (int)$store->getGroupId(),
                    'group_name'       => $group->getName(),
                    'is_active'        => (int)$store->getIsActive(),
                    'name'             => $store->getName(),
                    'secure_url'       => Mage::getModel('core/url')->setStore($store->getId())->getUrl("/", array("_secure" => true, '_nosid' => true)),
                    'sort_order'       => (int)$store->getSortOrder(),
                    'store_id'         => (int)$store->getId(),
                    'unsecure_url'     => Mage::getModel('core/url')->setStore($store->getId())->getUrl("/", array('_nosid' => true)),
                    'vat'              => $store->getConfig('general/store_information/merchant_vat_number'),
                    'website_id'       => (int)$store->getWebsiteId(),
                    'website_name'     => $website->getName(),
                    'root_category_id' => (int)$group->getRootCategoryId(),
                    'config'         => array(
                        'tax' => array(
                            'calculation' => array(
                                'price_includes_tax'         => (int)$store->getConfig('tax/calculation/price_includes_tax'),
                                'default_country'            => Mage::getStoreConfig('shipping/origin/country_id', $store),
                                'default_region'             => Mage::getStoreConfig('shipping/origin/region_id', $store),
                                'default_postcode'           => Mage::getStoreConfig('shipping/origin/postcode', $store),
                                'default_tax_dest_country'   => Mage::getStoreConfig('tax/defaults/country', $store),
                                'default_tax_dest_region'    => Mage::getStoreConfig('tax/defaults/region', $store),
                                'default_tax_dest_postcode'  => Mage::getStoreConfig('tax/defaults/postcode', $store),
                                'based_on'                   => $store->getConfig('tax/calculation/based_on'),
                                'default_customer_tax_class' => Mage::getModel('tax/calculation')->getDefaultCustomerTaxClass($store),
                                'apply_discount_on_prices'   => (int)$store->getConfig('tax/calculation/discount_tax'),
                            )
                        ),
                        'catalog' => array(
                            'show_savings_badge'     => (bool)((int)$store->getConfig('bakerloorestful/catalog/show_savings_badge')),
                            'simple_tap_addtobasket' => (bool)((int)$store->getConfig('bakerloorestful/catalog/simple_tap_addtobasket'))
                        )
                    ),
                    'allowed_currencies'             => $store->getConfig('currency/options/allow'),
                    'email_receipt'                  => (bool)($store->getConfig('bakerloorestful/pos_receipt/receipts') != 'magento'),
                    'simple_configurable_price'      => (bool)((int)$store->getConfig('bakerloorestful/general/simple_configurable_prices')),
                    'default_customer_group'         => (int)$store->getConfig('customer/create_account/default_group'),
                    'allow_customer_group_selection' => (int)$store->getConfig('bakerloorestful/new_customer_account/allow_customer_group_selection'),
                    'newsletter_subscribe_checked'   => (int)$store->getConfig('bakerloorestful/checkout/newsletter_subscribe_checked'),
                );

                $storeAddress = Mage::helper('bakerloo_restful')->getStoreAddress($id);

                $result = array_merge($storeData, $storeAddress);

            } catch(Exception $ex) {
                $result = null;
                Mage::logException($ex);
            }

        }

        return $result;

    }

}