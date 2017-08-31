<?php

class Ebizmarts_BakerlooRestful_Model_V1_Stores extends Ebizmarts_BakerlooRestful_Model_V1_Api {

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
            $group   = Mage::app()->getGroup((int)$store->getGroupId());

            $result = array(
                'address_street'   => $store->getConfig('general/store_information/address'),
                'code'             => $store->getCode(),
                'country'          => (string)$store->getConfig('general/country/default'),
                'currency_id'      => (string)$store->getConfig('currency/options/default'),
                'email'            => (string)$store->getConfig('trans_email/ident_general/email'),
                'group_id'         => (int)$store->getGroupId(),
                'group_name'       => $group->getName(),
                'is_active'        => (int)$store->getIsActive(),
                'name'             => $store->getName(),
                'postal_code'      => $store->getConfig('general/store_information/postal_code'),
                'region_id'        => $store->getConfig('general/store_information/region_id'),
                'secure_url'       => $store->getConfig('web/secure/base_url'),
                'sort_order'       => (int)$store->getSortOrder(),
                'store_id'         => (int)$store->getId(),
                'telephone'        => $store->getConfig('general/store_information/phone'),
                'unsecure_url'     => $store->getConfig('web/unsecure/base_url'),
                'vat'              => $store->getConfig('general/store_information/merchant_vat_number'),
                'website_id'       => (int)$store->getWebsiteId(),
                'website_name'     => $website->getName(),
                'root_category_id' => (int)$group->getRootCategoryId(),
                'config'         => array(
                    'tax' => array(
                        'calculation' => array(
                            'price_includes_tax' => (int)$store->getConfig('tax/calculation/price_includes_tax')
                        )
                    )
                ),
                'allowed_currencies' => $store->getConfig('currency/options/allow'),
            );

        }

        return $result;

    }

}