<?php

class Ebizmarts_BakerlooRestful_Model_V1_Taxes extends Ebizmarts_BakerlooRestful_Model_V1_Api {

    protected $_model = "tax/calculation_rate_title";

    /**
     * Process GET requests.
     * Return PRODUCT tax classes with rates.
     *
     * @return type
     * @throws Exception
     */
    public function get() {

        $taxes = array();
        $proc  = array();

        $collectionRule = Mage::getModel('tax/calculation_rule')
            ->getCollection()
            ->addCustomerTaxClassesToResult()
            ->addProductTaxClassesToResult()
            ->addRatesToResult();

        if($collectionRule->getSize()) {

            foreach ($collectionRule as $taxRule) {
                $taxClasses = $taxRule->getProductTaxClasses();
                if(count($taxClasses)) {
                    foreach($taxClasses as $_taxC) {
                        foreach($taxRule->getTaxRates() as $_rate) {

                            if(in_array(((int)$_rate), $proc)) {
                                continue;
                            }

                            array_push($proc, ((int)$_rate));

                            $rt = Mage::getModel('tax/calculation_rate')->load($_rate);

                            if($rt->getId()) {
                                $_tax = array(
                                                'country_id' => $rt->getTaxCountryId(),
                                                'region_id'  => $rt->getTaxRegionId(),
                                                'tax_class'  => (int)$_taxC,
                                                'rate'       => (float)$rt->getRate(),
                                             );

                                $taxes []= $_tax;
                            }

                        }
                    }
                }
            }

        }

        return $taxes;

    }

}