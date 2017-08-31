<?php
/*
 * Developer: Michael Jacky
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 */ 
    class Glace_Extendedordergrid_Block_Adminhtml_Order_View extends Mage_Adminhtml_Block_Template{
        
        
        protected function _construct()
        {
            $this->setTemplate('ciextendedordergrid/order_view.phtml');
        }
        
        protected function getMappedColumns(){
            return Mage::getModel("ciextendedordergrid/order_item")->getMappedColumns();
        }
        
        protected function getAttributes(){
            return Mage::getModel("ciextendedordergrid/order_item")->getAttributes();
        }
        
        protected function getViewData(){
            $orderItem = Mage::getModel("ciextendedordergrid/order_item");
            
            $collection = $orderItem->getCollection();
            $collection->getSelect()->join(
                array(
                    'order_item' => $collection->getTable('sales/order_item')
                ),
                'main_table.item_id = order_item.item_id', 
                array('order_item.product_id')
            );
            
            $collection->getSelect()->where(
                $collection->getConnection()->quoteInto('order_item.order_id = ?', $this->getOrderId()) 
            );
            $ret = $collection->getData();
            $showImages = Mage::getStoreConfig('ciextendedordergrid/general/images');
            
            if (intval($showImages) > 0) {
                foreach($ret as &$el){
                    $product = Mage::getModel('catalog/product')->load($el['product_id']);
                    
                    if ($product->getThumbnail() !== NULL && $product->getThumbnail() != 'no_selection' ){
                    	//Zend_Debug::dump($product->getThumbnail());die;
                        $el["thumbnail_url"] = $product->getThumbnailUrl() ;
                    }
                }
            }
            
            return $ret;
        }
    }
?>