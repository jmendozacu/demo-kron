<?php

	class Webkul_Preorder_Model_Preorderaction {

		public function toOptionArray()	{
			$data =  array(
						array('value'=>'0', 'label'=>'Per Product'),
						array('value'=>'1', 'label'=>'All Products'),
						array('value'=>'2', 'label'=>'Few Products'),
						array('value'=>'3', 'label'=>'All Product Except some'),
					);
	        return $data;
		}
	}