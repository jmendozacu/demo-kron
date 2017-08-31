<?php

class Devinc_Groupdeals_Adminhtml_SubscribersController extends Mage_Adminhtml_Controller_Action
{ 
	public function indexAction() {
		$this->loadLayout()
			->_title($this->__('Manage Subscribers'))
			->_setActiveMenu('groupdeals/subscribers')
			->renderLayout();
	}	
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('groupdeals/subscribers');
				 
				$model->load($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Customer was successfully unsubscribed!'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/');
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $subscriberIds = $this->getRequest()->getParam('subscribers');
        if(!is_array($subscriberIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select subscriber(s)'));
        } else {
            if (!empty($subscriberIds)) {
            	try {
            	    foreach ($subscriberIds as $subscriberId) {
            	        $subscriber = Mage::getModel('groupdeals/subscribers')->load($subscriberId);
            	        $subscriber->delete();
            	    }
            	    Mage::getSingleton('adminhtml/session')->addSuccess(
            	        Mage::helper('adminhtml')->__(
            	            'Total of %d subscriber(s) were successfully unsubscribed.', count($subscriberIds)
            	        )
            	    );
            	} catch (Exception $e) {
            	    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            	}
            }
        }
        $this->_redirect('*/*/');
    }   
  
    public function exportCsvAction()
    {
        $fileName   = 'Subscribers.csv';
        $content    = $this->getLayout()->createBlock('groupdeals/adminhtml_subscribers_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }  	

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }	
}