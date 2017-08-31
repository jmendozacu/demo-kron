<?php

class Ebizmarts_BakerlooPayment_Block_Info_Manualcc extends Ebizmarts_BakerlooPayment_Block_Info_Default {

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml() {

        $output = $this->getMethod()->getTitle();

        if($this->getInfo()->getPoNumber()) {
            $output .= "<br />" . $this->__("Authorization Number: %s", $this->getInfo()->getPoNumber());
        }

        return $output;
    }

}