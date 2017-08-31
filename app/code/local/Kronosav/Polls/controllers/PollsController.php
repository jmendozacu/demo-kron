<?php 
class Kronosav_Polls_PollsController extends Mage_Core_Controller_Front_Action
{ 
    public function preDispatch()
	{
		parent::preDispatch();
		$action = $this->getRequest()->getActionName();
		$loginUrl = Mage::helper('customer')->getLoginUrl();
	 
		if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
			$this->setFlag('', self::FLAG_NO_DISPATCH, true);
		}
	}
    public function indexAction()
	{
	  $this->loadLayout();
	  $this->renderLayout();	  
	  
	}
	public function saveAction()
	{
		$pollIds    = $this->getRequest()->getPost();
				
		foreach ($pollIds as $pollId => $answerId) {
			$poll = Mage::getModel('poll/poll')->load($pollId);
			 if ($poll->getId()) {
				$vote = Mage::getModel('poll/poll_vote')
					->setPollAnswerId($answerId)
					->setPollId($pollId)
					->setIpAddress(Mage::helper('core/http')->getRemoteAddr(true))
					->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId());

				$poll->addData($vote);
				$vote->save();
			}
		}		
		$data = array('status'=> "success", 'message'=>$this->getLayout()->createBlock('cms/block')->setBlockId('polls_vote_success_block')->toHtml());
		$this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));
        
	 }
}

