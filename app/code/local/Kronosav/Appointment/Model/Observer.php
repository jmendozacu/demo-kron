<?php
/**
 * @package jQuery Library.
 * @author: A.A.Treitjak
 * @copyright: 2012 - 2013 BelVG.com
 */

class Kronosav_Appointment_Model_Observer
{
    /**
     * Added jQuery library.
     *
     * @param Varien_Event_Observer $observer
     *
     * @return string
     */
    public function prepareLayoutBefore(Varien_Event_Observer $observer)
    {
       
        /* @var $block Mage_Page_Block_Html_Head */
        $block = $observer->getEvent()->getBlock();

        if ("head" == $block->getNameInLayout()) {
            foreach (Mage::helper('appointment')->getFiles() as $file) {
                $block->addJs(Mage::helper('appointment')->getJQueryPath($file));
            }
        }

        return $this;
    }
}
