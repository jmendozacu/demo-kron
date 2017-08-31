<?php
class MW_SocialGift_Model_Observer_Layout extends Mage_Core_Model_Abstract
{
    protected $_base_url;
    protected $_multi_path_js;
    protected $baseDir;

    public function controllerLayoutBefore(Varien_Event_Observer $observer)
    {
        $this->baseDir = Mage::getBaseDir();

        foreach (Mage::app()->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                foreach ($stores as $store) {
                    $this->_multi_path_js[$store->getCode()]        = $this->baseDir . "/media/mw_socialgift/js/" . $_SERVER['SERVER_NAME'] . "-socialgift-config-" . $store->getCode() . ".js";
                }
            }
        }
        if(Mage::getDesign()->getArea() == 'frontend') {
            $this->createConfigFile();
        }
    }

    protected function createConfigFile()
    {
        $base_url        = explode("/", Mage::getBaseUrl('js'));
        $base_url        = explode($base_url[count($base_url) - 2], Mage::getBaseUrl('js'));
        $this->_base_url = $base_url[0];

        $base_URLs   = preg_replace('/http:\/\//is', 'https://', $base_url[0]);

        /** Create file config for javascript */
        $js = "var mw_baseUrl = '{BASE_URL}';\n";

        $js = str_replace("{BASE_URL}", $base_url[0], $js);
        $js .= "var mw_baseUrls = '$base_URLs';\n";

        $js .= "var FACEBOOK_ID = '". Mage::helper('mw_socialgift')->getFBID() ."';\n";

        $file = new Varien_Io_File();
        $file->checkAndCreateFolder($this->baseDir . "/media/mw_socialgift/js/");
        $file->checkAndCreateFolder($this->baseDir . "/media/mw_socialgift/css/");
              {
            foreach ($this->_multi_path_js as $code => $path) {
                if(!file_exists($path) && Mage::app()->getStore()->getCode() == $code) {
                    $file->write($path, $js);
                    $file->close();
                }
            }
        }
        return $js;
    }
}