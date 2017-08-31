<?php
class ModuleMart_Deliverydate_Block_Adminhtml_Order_View_Tab_Delivery_Button extends Mage_Adminhtml_Block_Sales_Order_View
{
     /*
     **
     * Retrieve available order
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if ($this->hasOrder()) {
            return $this->getData('order');
        }
        if (Mage::registry('current_order')) {
            return Mage::registry('current_order');
        }
        if (Mage::registry('order')) {
            return Mage::registry('order');
        }
        Mage::throwException(Mage::helper('sales')->__('Cannot get the order instance.'));
    }
	
	public function __construct()
    {
        $this->_addButton('deliverydate', array(
            'label'     => Mage::helper('adminhtml')->__('Delivery Date'),
            'onclick'   => 'deliveryDate()',
            'class'     => 'save'
        ));
        
        $this->_addButton('pickupdate', array(
        		'label'     => Mage::helper('adminhtml')->__('Pickup Date'),
        		'onclick'   => 'pickupDate()',
        		'class'     => 'save'
        ));
		
        parent::__construct();
    }
	//'onclick'   => 'testAbc(\''.Mage::helper("adminhtml")->getUrl("deliverydate/adminhtml_deliverydate/send/",array("order_id"=>$this->getOrder()->getId(),"real_order_id"=>$this->getOrder()->getIncrementId())).'\')',
} 
?>