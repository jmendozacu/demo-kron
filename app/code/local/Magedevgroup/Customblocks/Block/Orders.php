<?php
class Magedevgroup_Customblocks_Block_Orders extends Mage_Core_Block_Template{

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * @return mixed|Varien_Data_Collection_Db
     */
    protected function getOrderCollection()
    {
        $storeId    = Mage::app()->getStore()->getId();

        /** @var Mage_Sales_Model_Resource_Order_Collection $ordersModel */
        $ordersModel = Mage::getModel('sales/order')->getCollection();

        $orders = $ordersModel->setOrder('created_at', 'desc');
        $orders->getSelect()->order('RAND()');
        $orders->setPageSize(3);

        $this->_orderCollection = $orders;

        return $this->_orderCollection;
    }
}