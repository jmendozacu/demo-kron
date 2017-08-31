<?php

class Kronosav_Polls_Block_Polls extends Mage_Core_Block_Template  
{
     public function _getPollsById()
	 {
	    $customer_id = Mage::getSingleton('customer/session')->getCustomerId();
	   
		 /**
		  * Get the resource model
		  */
		$resource = Mage::getSingleton('core/resource');
		 
		/**
		 * Retrieve the read connection
		 */
		$readConnection = $resource->getConnection('core_read');
		 
		/**
		 * Retrieve our table name
		 */
		$table = $resource->getTableName('poll/poll_vote');
	 
		$query = 'SELECT * FROM ' . $table . ' WHERE customer_id ='.$customer_id;
		$getPolls = $readConnection->fetchCol($query);
		return $getPolls;
		
	 }
	 public function _getPollsCollection()
	 {
	    $collection = Mage::getModel('poll/poll')->getCollection();
		return $collection;
	 }
	 
	 public function _getPollsanswer($poll_id)
	 {
	   $poll_answers = Mage::getModel('poll/poll_answer')
                        ->getCollection()
						->addFieldToSelect('*')
                        ->addFieldToFilter('poll_id',$poll_id)
						->getData();
		return $poll_answers;
	 }
}


