<?php
	class Webkul_Preorder_Adminhtml_PreorderController extends Mage_Adminhtml_Controller_action {
		
		protected function _initAction() {
			$this->loadLayout()
				->_setActiveMenu('preorder/items')
				->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			
			return $this;
		}

		public function indexAction() {
			$this->_initAction()
				->renderLayout();
		}

		public function massEmailAction() {
			$helper = Mage::helper("preorder");
			$customerArray=array();
			$productArray=array();
			$preOrderIdArray=array();
			$orderIds = $this->getRequest()->getParam('preorder');

			if(!is_array($orderIds)) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
			} else {
				try {
					$collection = Mage::getModel("preorder/preorder")->getCollection()
																	 ->addFieldTofilter("orderid",array('in'=>$orderIds))
																	 ->addFieldTofilter("notify",0);

					foreach ($collection as $item) {
						$productId = $item->getItemid();
						$stockStatus = $helper->getStockStatus($productId);
						// if($stockStatus==1) {
							$customerArray[] = $item->getCustomerId();
							$productArray[] = $item->getItemid();
							$preOrderIdArray[] = $item->getPreorderId();
						// }
					}
					$helper->sendEmailFromAdmin($customerArray, $productArray, $preOrderIdArray);
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Email sent successfully to Customer(s)'));
				} catch (Exception $e) {
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				}
			}
			$this->_redirect('*/*/index');
		}


		public function exportCsvAction() {
			$fileName   = 'preorder.csv';
			$content    = $this->getLayout()->createBlock('preorder/adminhtml_preorder_grid')
							->getCsv();

			$this->_sendUploadResponse($fileName, $content);
		}

		public function exportXmlAction() {
			$fileName   = 'preorder.xml';
			$content    = $this->getLayout()->createBlock('preorder/adminhtml_preorder_grid')->getXml();

			$this->_sendUploadResponse($fileName, $content);
		}

		protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream') {
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