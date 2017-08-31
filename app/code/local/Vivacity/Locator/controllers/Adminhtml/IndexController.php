<?php
class Vivacity_Locator_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
  protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('locator/set_time')
                ->_addBreadcrumb('locator Manager','locator Manager');
       return $this;
     }
      public function indexAction()
      {
         $this->_initAction();
         $this->renderLayout();
      }
      public function editAction()
      {
           $testId = $this->getRequest()->getParam('id');
           $testModel = Mage::getModel('locator/locator')->load($testId);
           if ($testModel->getId() || $testId == 0)
           {
             Mage::register('locator_data', $testModel);
             $this->loadLayout();
             $this->_setActiveMenu('locator/set_time');
             $this->_addBreadcrumb('locator Manager', 'locator Manager');
             $this->_addBreadcrumb('Locator Description', 'Locator Description');
             $this->getLayout()->getBlock('head')
                  ->setCanLoadExtJs(true);
             $this->_addContent($this->getLayout()
                  ->createBlock('locator/adminhtml_locator_edit'))
                  ->_addLeft($this->getLayout()
                  ->createBlock('locator/adminhtml_locator_edit_tabs')
              );
             $this->renderLayout();
           }
           else
           {
                 Mage::getSingleton('adminhtml/session')
                       ->addError('Locator does not exist');
                 $this->_redirect('*/*/');
            }
       }
       public function newAction()
       {
          $this->_forward('edit');
       }
       public function saveAction()
       {
         if ($this->getRequest()->getPost())
         {
		$postData = $this->getRequest()->getPost();

		if(isset($_FILES['custom_icon']['name']) && $_FILES['custom_icon']['name'] != '') {

					$uploader = new Varien_File_Uploader('custom_icon');
					$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					$uploader->setFilesDispersion(false);
					$path = Mage::getBaseDir('media').DS."storeLocator". DS ;
					$uploader->save($path, $_FILES['custom_icon']['name'] );
					$postData['custom_icon'] = $_FILES['custom_icon']['name'];
		} else {
			$postImagea = $this->getRequest()->getPost('custom_icon');
			$postData['custom_icon'] = $postImagea['value'];
		}

if(isset($_FILES['store_image']['name']) && $_FILES['store_image']['name'] != '') {

					$uploader = new Varien_File_Uploader('store_image');
					$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					$uploader->setFilesDispersion(false);
					$path = Mage::getBaseDir('media').DS."storeLocator". DS ;
					$uploader->save($path, $_FILES['store_image']['name'] );
					$postData['store_image'] = $_FILES['store_image']['name'];
		} else {
			$postImage = $this->getRequest()->getPost('store_image');
			$postData['store_image'] = $postImage['value'];
		}

           try {
                 
                 $testModel = Mage::getModel('locator/locator');
               if( $this->getRequest()->getParam('id') <= 0 )
                  $testModel->setCreatedTime(
                     Mage::getSingleton('core/date')
                            ->gmtDate()
                    );
                  $testModel
                    ->addData($postData)
                    ->setUpdateTime(
                             Mage::getSingleton('core/date')
                             ->gmtDate())
                    ->setId($this->getRequest()->getParam('id'))
                    ->save();
                 Mage::getSingleton('adminhtml/session')
                               ->addSuccess('successfully saved');
                 Mage::getSingleton('adminhtml/session')
                                ->setlocatorData(false);
                 $this->_redirect('*/*/');
                return;
          } catch (Exception $e){
                Mage::getSingleton('adminhtml/session')
                                  ->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')
                 ->setlocatorData($this->getRequest()
                                    ->getPost()
                );
                $this->_redirect('*/*/edit',
                            array('id' => $this->getRequest()
                                                ->getParam('id')));
                return;
                }
              }
              $this->_redirect('*/*/');
            }
          public function deleteAction()
          {
              if($this->getRequest()->getParam('id') > 0)
              {
                try
                {
                    $testModel = Mage::getModel('locator/locator');
                    $testModel->setId($this->getRequest()
                                        ->getParam('id'))
                              ->delete();
                    Mage::getSingleton('adminhtml/session')
                               ->addSuccess('successfully deleted');
                    $this->_redirect('*/*/');
                 }
                 catch (Exception $e)
                  {
                           Mage::getSingleton('adminhtml/session')
                                ->addError($e->getMessage());
                           $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                  }
             }
            $this->_redirect('*/*/');
       }
	public function exportCsvAction() {
    		$fileName   = 'locator_csv.csv';
    		$content    = $this->getLayout()->createBlock('locator/adminhtml_locator_grid')->getCsvFile();
    		$this->_prepareDownloadResponse($fileName, $content);
	}
	public function exportExcelAction() {
    		$fileName   = 'locator_csv.ods';
    		$content    = $this->getLayout()->createBlock('locator/adminhtml_locator_grid')->getExcelFile();
    		$this->_prepareDownloadResponse($fileName, $content);
	}
	protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream') {
		$this->_prepareDownloadResponse($fileName, $content, $contentType);
	}
}
