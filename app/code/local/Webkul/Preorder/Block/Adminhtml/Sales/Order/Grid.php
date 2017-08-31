<?php

	class Webkul_Preorder_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid {

		protected function _getCollectionClass() {
			return "sales/order_grid_collection";
		}

		protected function _prepareCollection() {
			$collection = Mage::getResourceModel($this->_getCollectionClass());
			$prefix = Mage::getConfig()->getTablePrefix();
			$collection->getSelect()->joinLeft(array("wk_preorder" => $prefix."wk_preorder"),"wk_preorder.orderid = main_table.entity_id",array("orderid" => "orderid",'rand'=>'wk_preorder.rand'))->where('wk_preorder.rand=1 OR 1')->distinct('wk_preorder.orderid');
			$collection->addFilterToMap("rand","wk_preorder.rand");
			$collection->addFilterToMap("status","main_table.status");
			$this->setCollection($collection);
			Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
		}

		protected function _prepareColumns()  {
			$this->addColumn("rand", array(
				"header"	=>	$this->__("Order Mode"),
				"align"		=>	"center",
				"width"		=>	"100px",
				"height"	=>	"100px",
				"type"		=>	"options",
				'index'		=>	'rand',
				'options'	=>	$this->values()
			));
			return parent::_prepareColumns();
		}

		public function getRowUrl($row) {
			return $this->getUrl("*/*/view", array("order_id" => $row->getId()));
		}

		protected function values() {
			return array("" => Mage::helper("preorder")->__("Normal"),"1" => Mage::helper("preorder")->__("Preorder"));
		}
	}