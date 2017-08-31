<?php
class ModuleMart_Deliverydate_Block_Adminhtml_Order_View_Tab_Delivery_Tab extends Mage_Adminhtml_Block_Widget_Tabs
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
        parent::__construct();
        $this->setId('sales_order_view_tabs');
        $this->setDestElementId('sales_order_view');
        $this->setTitle(Mage::helper('sales')->__('Order View'));
    }
	
	protected function _beforeToHtml(){
		$this->addTab('deliverydate', array(
			'label'     => Mage::helper('sales')->__('Delivery/Pickup Date'),
			'content'   => $this->deliveryDate(),
		));
		$this->_updateActiveTab();
        Varien_Profiler::stop('sales/tabs');
        return parent::_beforeToHtml();
	}
	
	public function deliveryDate() {
		
		// get delivery date for current order
		$collection = Mage::getModel('deliverydate/deliverydate')->getCollection();
		$collection->addFieldToFilter('order_id',array('eq'=>$this->getOrder()->getId()));
		
		
		$isPickupDate = $collection->getFirstItem()->getIsPickupDate();
		
		if($isPickupDate != "1"){
			$deliveryDate = $collection->getFirstItem()->getDeliveryDate();
		}else{
			$pickupDate = $collection->getFirstItem()->getDeliveryDate();
		}
		
		$data = '<div class="entry-edit-head">
				<h4 class="icon-head head-edit-form fieldset-legend">Delivery/Pickup Date</h4>
				<div class="form-buttons"></div>
				</div>';
		
		$data .='<div class="fieldset"><div class="hor-scroll">
					<table cellspacing="0" class="form-list">
					<tbody>
					<tr><td class="label"><label for="delivert-date">Delivery Date</label></td>
					<td class="value">
					<span>
					<img style="margin-top: 3px; position: absolute; margin-left: -20px;" title="Date" id="date_select_trig" src="'.$this->getSkinUrl("images/grid-cal.gif").'"/>
					<input type="text" style="width: 140px;" class="input-text required-entry" value="'.$deliveryDate.'" id="selected_date" name="selected_date"/>
					<script type="text/javascript">
					//<![CDATA[
						Calendar.setup({
							inputField: "selected_date",
							ifFormat: "%m/%e/%Y",
							showsTime: true,
							button: "date_select_trig",
							align: "Bl",
							singleClick : true
						});
						
						function deliveryDate() {
							window.location="'.Mage::helper("adminhtml")->getUrl("deliverydate/adminhtml_deliverydate/send/").'?order_id='.$this->getOrder()->getId().'&real_order_id='.$this->getOrder()->getIncrementId().'&deliverydate="+document.getElementById("selected_date").value;
						}
					//]]>
					</script>
				</span></td></tr>
									
					<tr><td class="label"><label for="delivert-date">Pickup Date</label></td>
					<td class="value">
					<span>
					<img style="margin-top: 3px; position: absolute; margin-left: -20px;" title="Date" id="pickupdate_select_trig" src="'.$this->getSkinUrl("images/grid-cal.gif").'"/>
					<input type="text" style="width: 140px;" class="input-text required-entry" value="'.$pickupDate.'" id="pickup_date" name="pickup_date"/>
					<script type="text/javascript">
					//<![CDATA[
						Calendar.setup({
							inputField: "pickup_date",
							ifFormat: "%m/%e/%Y",
							showsTime: true,
							button: "pickupdate_select_trig",
							align: "Bl",
							singleClick : true
						});
						
						function pickupDate() {
							window.location="'.Mage::helper("adminhtml")->getUrl("deliverydate/adminhtml_pickupdate/send/").'?order_id='.$this->getOrder()->getId().'&real_order_id='.$this->getOrder()->getIncrementId().'&pickupdate="+document.getElementById("pickup_date").value;
						}
					//]]>
					</script>
				</span></td></tr>
					</tbody>
					</table>
				</div></div>';
				
		return $data;					
	}
		
	//function deliveryDate() {
					//		var dates = document.getElementById("selected_date").value;
						///	
							//window.location="'.Mage::helper("adminhtml")->getUrl("deliverydate/adminhtml_deliverydate/send/",array("order_id"=>$this->getOrder()->getId(),"real_order_id"=>$this->getOrder()->getIncrementId())).'date="'+dates;
			//$data .='				
				//		}
	 protected function _updateActiveTab(){
        $tabId = $this->getRequest()->getParam('tab');
        if( $tabId ) {
            $tabId = preg_replace("#{$this->getId()}_#", '', $tabId);
            if($tabId) {
                $this->setActiveTab($tabId);
            }
        }
    }
	
	/* public function _constuct()
    {
        parent::_construct();
        $this->setTemplate('modulemart/deliverydate/order/view/tab/delivery.phtml');
    }*/
	

    /*public function getTabLabel() {
        return $this->__('Delivery Date');
    }

    public function getTabTitle() {
        return $this->__('Delivery Date');
    }

    public function canShowTab() {
        return true;
    }

    public function isHidden() {
        return false;
    }*/

} 
?>