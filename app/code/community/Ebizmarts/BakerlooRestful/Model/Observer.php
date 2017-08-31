<?php

class Ebizmarts_BakerlooRestful_Model_Observer {

    protected function _canProfile($controllerAction) {
        $isMod        = (bool) ('Ebizmarts_BakerlooRestful' == $controllerAction->getRequest()->getControllerModule());
        $debugEnabled = ((int)Mage::helper('bakerloo_restful')->config("general/debug") === 1);

        return (bool)($isMod && $debugEnabled);
    }

    public function profilePre($o) {
        $ca = $o->getEvent()->getControllerAction();
        if ($this->_canProfile($ca) === FALSE) {
            return $o;
        }

        Mage::getSingleton('core/resource')->getConnection('core_write')->getProfiler()->setEnabled(TRUE);
        //Varien_Profiler::enable();
    }

    public function profilePost($o) {
        $ca = $o->getEvent()->getControllerAction();
        if ($this->_canProfile($ca) === FALSE) {
            return $o;
        }

        Mage::helper('bakerloo_restful')->logprofiler($ca);
    }

    /**
     * Add button to Cache Management to be able to clear thumbs generated for categories.
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Varien_Event_Observer
     */
    public function clearCategoryImageCacheButton(Varien_Event_Observer $observer) {
        $block = $observer->getEvent()->getBlock();

        if ($block instanceof Mage_Adminhtml_Block_Cache) {
            $message = Mage::helper('bakerloo_restful')->__('Are you sure?');
            $clearUrl = $block->getUrl('adminhtml/bakerloo/clearCategoryImagesCache');

            $block->addButton('flush_pos_category_images', array(
                'label'     => Mage::helper('bakerloo_restful')->__('Flush POS category images cache'),
                'onclick'   => 'confirmSetLocation(\''.$message.'\', \'' . $clearUrl .'\')',
                'class'     => 'delete',
            ));
        }

        return $observer;
    }

    public function httpResponseDebug(Varien_Event_Observer $observer) {
        $requestId = Mage::registry('brest_request_id');

        if($requestId) {
            $response = $observer->getEvent()->getResponse();
            Mage::helper('bakerloo_restful')->debug($response);
        }

        return $observer;
    }
}