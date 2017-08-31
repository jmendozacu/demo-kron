<?php

class Ebizmarts_BakerlooRestful_Adminhtml_BakerlooController extends Mage_Adminhtml_Controller_Action {

    protected function _ajaxRedirectResponse() {
        $this->getResponse()
            ->setHeader('HTTP/1.1', '403 Session Expired')
            ->setHeader('Login-Required', 'true')
            ->sendResponse();
        return $this;
    }

    public function generatekeyAction() {
        $this->getResponse()->setBody($this->_generateKey());
    }

    private function _generateKey() {

        $key = "";

        try {
            $entropy = "";
            // in case /dev/urandom is reusing entropy from its pool, let's add a bit more entropy
            $entropy .= uniqid(mt_rand(), true);
            $hash = sha1($entropy); // sha1 gives us a 40-byte hash
            // The first 30 bytes should be plenty for the consumer_key
            $key .= substr($hash, 0, 30);

        } catch (Exception $ex) {
            $key = Mage::helper("core")->getRandomString(30);
        }

        return $key;
    }

    public function generateactivationkeyAction() {
        $this->getResponse()->setBody($this->_generateActivationKey());
    }

    private function _generateActivationKey() {

        try {

            $magentoDomain = Mage::helper('bakerloo_restful')->getMagentoDomain();

            $apiPath = Mage::getModel('bakerloo_restful/api_api')->getApiPath();

            $text = $magentoDomain . "|" . $apiPath . "|" . date("Y-m-d");

            return Mage::helper('bakerloo_restful')->encryptActivationKey($text);

        } catch (Exception $ex) {
            return "Error: Unable to create activation key.";
        }
    }

    public function clearCategoryImagesCacheAction() {

        $imagesCacheDir = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'category' . DS . 'cache';

        try {

            foreach (new DirectoryIterator($imagesCacheDir) as $fileInfo) {
                if($fileInfo->isDot()){
                    continue;
                }

                unlink($fileInfo->getPathname());
            }

            if(rmdir($imagesCacheDir)) {
                $this->_getSession()->addSuccess(Mage::helper('bakerloo_restful')->__("POS category images cache cleared."));
            }
            else {
                $this->_getSession()->addError(Mage::helper('bakerloo_restful')->__("Cache dir could not be removed."));
            }

        }catch(Exception $ex) {
            $this->_getSession()->addError(Mage::helper('bakerloo_restful')->__("Cache dir could not be removed."));
        }

        $this->_redirect('adminhtml/cache/');
    }

}