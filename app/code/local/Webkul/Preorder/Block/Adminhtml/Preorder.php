<?php

	class Webkul_Preorder_Block_Adminhtml_Preorder extends Mage_Adminhtml_Block_Widget_Grid_Container {
		
		public function __construct() {
			$this->_controller = 'adminhtml_preorder';
			$this->_blockGroup = 'preorder';
			$this->_headerText = Mage::helper('preorder')->__('Preorder Manager');
			parent::__construct();
			$this->_removeButton('add');
			$this->_removeButton('reset_filter_button');
			$this->_removeButton('search_button');
		}
	}