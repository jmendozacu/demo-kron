<?php

class Ebizmarts_BakerlooRestful_Model_Api_Currencyrates extends Ebizmarts_BakerlooRestful_Model_Api_Api {

    protected $_model = "directory/currency";

    /**
     * Process GET requests.
     *
     * @return type
     * @throws Exception
     */
    public function get() {

        $currencyModel = Mage::getModel('directory/currency');
        $currencies = $currencyModel->getConfigAllowCurrencies();
        $defaultCurrencies = $currencyModel->getConfigBaseCurrencies();
        $_currencies = $this->_prepareRates($currencyModel->getCurrencyRates($defaultCurrencies, $currencies));

        foreach( $_currencies as $key => $value ) {
            ksort($_currencies[$key]);
        }

        $myRates = array();
        foreach($_currencies as $rates) {
            $myRates []= $rates;
        }

        return $myRates;

    }

    protected function _prepareRates($array) {
        if( !is_array($array) ) {
            return $array;
        }

        $rates = array();

        foreach ($array as $key => $rate) {
            foreach ($rate as $code => $value) {
                $parts = explode('.', $value);

                if( !array_key_exists($key, $rates) ) {
                    $rates [$key] = array('currency_id' => $key, 'rates' => array());
                }

                if( sizeof($parts) == 2 ) {
                    $parts[1] = str_pad(rtrim($parts[1], 0), 4, '0', STR_PAD_RIGHT);
                    $rateFloat = join('.', $parts);
                } elseif( $value > 0 ) {
                    $rateFloat = number_format($value, 4);
                } else {
                    $rateFloat = null;
                }

                $rates[$key]['rates'][$code] = (float)$rateFloat;
            }
        }

        return $rates;
    }

}