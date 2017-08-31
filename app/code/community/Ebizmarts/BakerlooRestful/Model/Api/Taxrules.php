<?php

class Ebizmarts_BakerlooRestful_Model_Api_Taxrules extends Ebizmarts_BakerlooRestful_Model_Api_Api {

    /**
     * Model name.
     *
     * @var string
     */
    protected $_model = "tax/calculation_rate_title";

    /**
     * Process GET requests.
     * Return PRODUCT tax classes with rates.
     *
     * @return type
     * @throws Exception
     */
    public function get() {

        $taxes = $proc = $rules = array();

        $collectionRule = Mage::getModel('tax/calculation_rule')
            ->getCollection()
            ->addCustomerTaxClassesToResult()
            ->addProductTaxClassesToResult()
            ->addRatesToResult();

        if($collectionRule->getSize()) {

            foreach ($collectionRule as $taxRule) {

                $_rule = array(
                    'id'                   => (int)$taxRule->getId(),
                    'priority'             => (int)$taxRule->getPriority(),
                    'code'                 => $taxRule->getCode(),
                    'sort_order'           => (int)$taxRule->getPosition(),
                    'customer_tax_classes' => array_map('intval', array_values( array_unique($taxRule->getCustomerTaxClasses(), SORT_NUMERIC) ) ),
                    'product_tax_classes'  => array_map('intval', array_values( array_unique($taxRule->getProductTaxClasses(), SORT_NUMERIC) ) ),
                    'rates'                => array(),
                );

                $taxClasses = $taxRule->getProductTaxClasses();
                if(count($taxClasses)) {
                    foreach ($taxClasses as $_taxC) {
                        foreach ($taxRule->getTaxRates() as $_rate) {

                            if (in_array((((int)$_rate).$_taxC), $proc)) {
                                continue;
                            }

                            array_push($proc, (((int)$_rate).$_taxC));

                            $rt = Mage::getModel('tax/calculation_rate')->load($_rate);

                            if ($rt->getId()) {

                                if (!$rt->hasTaxPostcode()) {
                                    $rt->setTaxPostcode('*');
                                }

                                $_tax = array(
                                    'code'              => $rt->getCode(),
                                    'country_id'        => $rt->getTaxCountryId(),
                                    'region_id'         => $rt->getTaxRegionId(),
                                    'tax_class'         => (int)$_taxC,
                                    'rate'              => (float)$rt->getRate(),
                                    'postcode'          => $rt->getTaxPostcode(),
                                    'postcode_is_range' => (int)$rt->getZipIsRange(),
                                );

                                $_rule ['rates'][] = $_tax;
                            }

                        }
                    }
                }

                array_push($rules, $_rule);

            }

        }

        return $rules;

    }

}