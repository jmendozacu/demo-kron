<?php

class Ebizmarts_BakerlooRestful_Model_V1_Customers extends Ebizmarts_BakerlooRestful_Model_V1_Api {

    protected $_model = "customer/customer";

    /*public function checkGetPermissions() {
        //Validate permissions
        $this->checkPermission(array('bakerloo_api/login', 'bakerloo_api/customers/list'));
    }*/

    protected function _getCollection() {
        return Mage::getResourceModel('customer/customer_collection')
                        ->addNameToSelect()
                        ->addAttributeToSelect('bakerloo_payment_methods');
    }

    public function _createDataObject($id = null, $data = null) {

        $result = array();

        if(is_null($data)) {
            $customer = Mage::getModel($this->_model)->load($id);
        }
        else {
            $customer = $data;
        }

        if($customer->getId()) {

            $result['customer_id']              = (int) $customer->getId();
            $result['firstname']                = $customer->getFirstname();
            $result['lastname']                 = $customer->getLastname();
            $result['email']                    = $customer->getEmail();
            $result['website_id']               = (int) $customer->getWebsiteId();
            $result['group_id']                 = (int) $customer->getGroupId();
            $result['bakerloo_payment_methods'] = (string)$customer->getBakerlooPaymentMethods();

            if("0000-00-00 00:00:00" == $customer->getUpdatedAt()) {
                $result['updated_at'] = Mage::getModel('core/date')->gmtDate();
            }
            else {
                $result['updated_at'] = $customer->getUpdatedAt();
            }

            //address
            $result['address'] = array();

            $addresses = $customer->getAddressesCollection();
            if($addresses->getSize()) {

                $defaultBillingId  = (int)$customer->getDefaultBilling();
                $defaultShippingId = (int)$customer->getDefaultShipping();

                foreach($addresses as $_address) {

                    $id = (int)$_address->getId();

                    $addr = array(
                                   "customer_address_id" => $id,
                                   "firstname"           => $_address->getFirstname(),
                                   "lastname"            => $_address->getLastname(),
                                   "country_id"          => $_address->getCountryId(),
                                   "city"                => $_address->getCity(),
                                   "street"              => $_address->getStreet(-1),
                                   "region_id"           => $_address->getRegionId(),
                                   "region"              => $_address->getRegion(),
                                   "postcode"            => $_address->getPostcode(),
                                   "telephone"           => $_address->getTelephone(),
                                   "fax"                 => $_address->getFax(),
                                   "company"             => $_address->getCompany(),
                                   "is_shipping_address" => ($defaultShippingId == $id) ? 1 : 0,
                                   "is_billing_address"  => ($defaultBillingId == $id) ? 1 : 0
                                  );

                    $result['address'] []= $addr;
                }

            }

        }

        return $result;

    }

}