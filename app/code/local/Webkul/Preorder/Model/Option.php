<?php

	class Webkul_Preorder_Model_Option {

		public function toOptionArray()	{
			$data =  array(
						array('value'=>'0', 'label'=>'Complete Payment'),
						array('value'=>'1', 'label'=>'Percent Payment'),
					);
	        return $data;
		}
	}