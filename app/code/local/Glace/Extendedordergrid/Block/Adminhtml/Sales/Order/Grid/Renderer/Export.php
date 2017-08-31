<?php
/*
 * Developer: Michael Jacky
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 */ 
class Glace_Extendedordergrid_Block_Adminhtml_Sales_Order_Grid_Renderer_Export extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected $_orderTableAlias = 'cmsideas_order_item';
    
    public function render(Varien_Object $row)
    {
        
        $entityId = $row->getData('entity_id');

        
        $index = $this->getColumn()->getIndex();
        $index = str_replace("{$this->_orderTableAlias}.", '', $index);
        $collection = Mage::getModel("ciextendedordergrid/order_item")->getCollection();
        
        $collection->getSelect()->join(
            array(
                'orderItem' => $collection->getTable('sales/order_item')
            ),
            'main_table.item_id = orderItem.item_id', 
            array()
        );
        
        $collection->addFieldToFilter("orderItem.order_id", $entityId);
        
        $html = array();
        
        $data = $collection->getData();
        
        foreach($data as $item){
            if (isset($item[$index]) && !empty($item[$index]))
                $html[] = $item[$index];
        }
        
        $val = count($html) > 0 ? implode(", ", $html) : '';
        
        return $val;
    }
}