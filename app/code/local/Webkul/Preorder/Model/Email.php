<?php

	class Webkul_Preorder_Model_Email {

		public function toOptionArray() {
			$data =  array(
						array('value'=>'0', 'label'=>'Automatic'),
						array('value'=>'1', 'label'=>'Manual'),
					);
			return $data;
		}
	}