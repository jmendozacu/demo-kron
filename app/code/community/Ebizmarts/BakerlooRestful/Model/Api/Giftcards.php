<?php

class Ebizmarts_BakerlooRestful_Model_Api_Giftcards extends Ebizmarts_BakerlooRestful_Model_Api_Api {

    protected $_model = "enterprise_giftcardaccount/giftcardaccount";

    /**
     * Process GET requests.
     *
     * @return array
     * @throws Exception
     */
    public function get() {

        $this->_verifyModuleInstalled();

        Mage::app()->setCurrentStore($this->getStoreId());

        $this->checkGetPermissions();

        $identifier = $this->_getIdentifier(true);

        if($identifier) { //get item by code

            if(!empty($identifier)) {
                return $this->_createDataObject($identifier);
            }
            else {
                throw new Exception('Incorrect request.');
            }

        }
        else {
            throw new Exception('Incorrect request.');
        }

    }

    public function _createDataObject($id = null, $data = null) {
        $result = new stdClass;

        $card = Mage::getModel('enterprise_giftcardaccount/giftcardaccount')
            ->loadByCode($id);

        try {
            $card->isValid(true, true, true, false);
        }
        catch (Mage_Core_Exception $e) {
            $card->unsetData();
        }

        if($card->getId()) {
            $result = $card->toArray();

            if(isset($result['giftcardaccount_id'])) {
                $result['id'] = (int)$result['giftcardaccount_id'];
                unset($result['giftcardaccount_id']);
            }

        }

        return $result;
    }

    /**
     * Validate provided gift card.
     * Receives an order and gift card code.
     *
     * PUT
     */
    public function put() {

        $this->_verifyModuleInstalled();

        if(!$this->getStoreId()) {
            Mage::throwException('Please provide a Store ID.');
        }

        Mage::app()->setCurrentStore($this->getStoreId());

        $data = $this->getJsonPayload();

        //Apply gift cards and validate
        $giftCards = $data->gift_card;

        if(empty($giftCards) or !is_array($giftCards)) {
            Mage::throwException(Mage::helper('bakerloo_restful')->__('No gift cards found in data.'));
        }

        $returnData = array();

        $quote = Mage::helper('bakerloo_restful/sales')->buildQuote($this->getStoreId(), $data, true);

        foreach($giftCards as $_giftCardCode) {
            Mage::getModel('enterprise_giftcardaccount/giftcardaccount')
                ->loadByCode($_giftCardCode)
                ->addToCart(false, $quote);
        }

        $quote->collectTotals()->save();

        $cartData = Mage::helper('bakerloo_restful/sales')->getCartData($quote);

        $quoteGiftCards = array();
        $_quoteGift     = $quote->getGiftCards();
        if(!empty($_quoteGift)) {
            $quoteGiftCards = unserialize($quote->getGiftCards());
            $quoteGiftCards = $this->_formatGiftCardResponse($quoteGiftCards);
        }

        $returnData = array(
            'order'              => $cartData,
            'applied_gift_cards' => $quoteGiftCards,
        );

        $quote->delete();

        return $returnData;

    }

    protected function _formatGiftCardResponse(array $quoteGiftCards) {

        for ($i=0; $i < count($quoteGiftCards); $i++) {
            if(isset($quoteGiftCards[$i]['i'])) {
                $quoteGiftCards[$i]['id'] = (int)$quoteGiftCards[$i]['i'];
                unset($quoteGiftCards[$i]['i']);
            }
            if(isset($quoteGiftCards[$i]['c'])) {
                $quoteGiftCards[$i]['code'] = $quoteGiftCards[$i]['c'];
                unset($quoteGiftCards[$i]['c']);
            }
            if(isset($quoteGiftCards[$i]['ba'])) {
                $quoteGiftCards[$i]['base_amount'] = $quoteGiftCards[$i]['ba'];
                unset($quoteGiftCards[$i]['ba']);
            }
            if(isset($quoteGiftCards[$i]['a'])) {
                $quoteGiftCards[$i]['amount'] = $quoteGiftCards[$i]['a'];
                unset($quoteGiftCards[$i]['a']);
            }
        }

        return $quoteGiftCards;
    }

    protected function _verifyModuleInstalled() {
        //If Magento is not enterprise, GiftCard is not available.
        $moduleInstalled = Mage::helper('bakerloo_restful')->isModuleInstalled('Enterprise_GiftCard');
        if(!$moduleInstalled) {
            Mage::throwException(Mage::helper('bakerloo_restful')->__("Feature is not available."));
        }
    }

 }