<?php

class Simi_Simicontact_Model_Observer {

    /**
     * process controller_action_predispatch event
     *
     * @return Magestore_VisitorSegmentation_Model_Observer
     */
    public function connectorConfigGetPluginsReturn($observer) {

        if ($this->getConfig("enable") == 0) {

            $observerObject = $observer->getObject();
            $observerData = $observer->getObject()->getData();
            $contactPluginId = NULL;
            foreach ($observerData['data'] as $key => $plugin) {
                if ($plugin['sku'] == 'simi_simicontact')
                    unset($observerData['data'][$key]);
            }
            $observerObject->setData($observerData);
        }
    }

    public function getConfig($value) {
        return Mage::getStoreConfig("simicontact/general/" . $value);
    }

}
