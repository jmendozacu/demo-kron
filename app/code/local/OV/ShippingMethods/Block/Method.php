<?php

class OV_ShippingMethods_Block_Method extends Mage_Checkout_Block_Onepage_Shipping_Method_Available
{
    const METHOD_FOR_ENABLE = 'velanapps_clickpick_dungannon';

    public function getShippingRates()
    {
        $remove = false;
        $rates = parent::getShippingRates();

        $items = $this->getQuote()->getAllItems();

        foreach ($items as $item) {
            $options =  $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
            foreach ($options as $option) {
                foreach ($option as $opt) {
                    //Checking value
                    if (!empty($opt) && !empty($opt['label']) && !empty($opt['value'])) {
                        if (($opt['label'] == 'Store pick up') &&
                            ($opt['value'] == "Yes I would like to pick up in store")
                        ) {
                            $remove = true;
                        }
                    }
                }
            }
        }

        foreach ($rates as $code => $_rates) {
            if ($remove == true) {
                if ($code != self::METHOD_FOR_ENABLE) {
                    unset($rates[$code]);
                }
            }
        }

        return $rates;
    }
}