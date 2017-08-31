<?php

class Devinc_Groupdeals_Adminhtml_MerchantsController extends Mage_Adminhtml_Controller_Action
{
	/**
     * Initialize merchants grid
     */
	public function indexAction() {		
		$this->loadLayout()
			->_setActiveMenu('groupdeals/merchants')
			->_title($this->__('Manage Merchants'))
			->renderLayout();
	}
	
	/**
     * MERCHANT EDIT functions
     */
	public function newAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('groupdeals/merchants')->load($id);

		if ($model->getId() || $id == 0) {								
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);			
			}
			
			Mage::register('merchant_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('groupdeals/merchants');
			$this->_title(false)->_title('New Merchant');
			
			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);			
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('groupdeals/adminhtml_merchants_edit'))
				->_addLeft($this->getLayout()->createBlock('groupdeals/adminhtml_merchants_edit_tabs'));
				
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('groupdeals')->__('This merchant does not exist.'));
			$this->_redirect('*/*/');
		}
	}
	
	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('groupdeals/merchants')->load($id);

		if ($model->getId() || $id == 0) {								
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);			
			}
			
			Mage::register('merchant_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('groupdeals/merchants');
			
			$storeId = $this->getRequest()->getParam('store', 0);
        	$name = Mage::getModel('license/module')->getDecodeString($model->getName(), $storeId);
			$this->_title(false)->_title($name);
			
			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);			
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('groupdeals/adminhtml_merchants_edit'))
				 ->_addLeft($this->getLayout()->createBlock('groupdeals/adminhtml_merchants_edit_tabs'));
				
			if (!Mage::app()->isSingleStoreMode() && ($switchBlock = $this->getLayout()->getBlock('store_switcher'))) {
				$switchBlock->setDefaultStoreName($this->__('Default Values'))
					->setWebsiteIds(array(1))
					->setSwitchUrl($this->getUrl('*/*/*', array('_current'=>true, 'active_tab'=>null, 'tab' => null, 'store'=>null)));
			}
				
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('groupdeals')->__('This merchant does not exist.'));
			$this->_redirect('*/*/');
		}
	}
	
    /**
     * if logged in as a merchant, view merchant's account info
     */
	public function accountAction() {
		if($merchant = Mage::getModel('groupdeals/merchants')->isMerchant()){
	    	$this->_forward('edit', null, null, array('id' => $merchant->getId()));
	    } else {
	    	$this->_redirect('groupdealsadmin/adminhtml_groupdeals/');
	    }
	}
		
    /**
     * save merchant
     */
	public function saveAction() {		
		if ($data = $this->getRequest()->getPost()) {	
			$merchantId = $this->getRequest()->getParam('id');
        	$storeId = $this->getRequest()->getParam('store', 0);	
	  			
	  		//encode address
			$address = '';
			for ($i = 1; $i<=5; $i++) {
				if ($data['address_'.$i]!='') {
					$address .= $data['address_'.$i].'_;_';
				}
			}
			$data['address'] = substr($address,0,-3);
			
			//set default if not set
			if (!isset($data['name_default'])) $data['name_default'] = 0;
			if (!isset($data['description_default'])) $data['description_default'] = 0;
			if (!isset($data['website_default'])) $data['website_default'] = 0;
			if (!isset($data['email_default'])) $data['email_default'] = 0;
			if (!isset($data['facebook_default'])) $data['facebook_default'] = 0;
			if (!isset($data['twitter_default'])) $data['twitter_default'] = 0;
			if (!isset($data['phone_default'])) $data['phone_default'] = 0;
			if (!isset($data['mobile_default'])) $data['mobile_default'] = 0;
			if (!isset($data['business_hours_default'])) $data['business_hours_default'] = 0;
			if (!isset($data['redeem_default'])) $data['redeem_default'] = 0;			
			
			//get previous merchant values
			if ($this->getRequest()->getParam('id', 0)!=0) {
				$merchant = Mage::getModel('groupdeals/merchants')->load($this->getRequest()->getParam('id'));
				$name = $merchant->getName();	
				$description = $merchant->getDescription();	
				$website = $merchant->getWebsite();	
				$email = $merchant->getEmail();	
				$facebook = $merchant->getFacebook();	
				$twitter = $merchant->getTwitter();	
				$phone = $merchant->getPhone();	
				$mobile = $merchant->getMobile();	
				$business_hours = $merchant->getBusinessHours();	
				$redeem = $merchant->getRedeem();	
			} else {
				$name = '';
				$description = '';
				$website = '';
				$email = '';
				$facebook = '';
				$twitter = '';
				$phone = '';
				$mobile = '';
				$business_hours = '';
				$redeem = '';
			}
			
			//add and encode new values
			if ($data['name']!='' || $data['name_default']==1) {
				$name_array = Mage::getModel('license/module')->getDecodeString($name);	
				if ($data['name_default']!=1) {
					$name_array[$storeId] = $data['name'];
				} else {
					unset($name_array[$storeId]);
				}
				$data['name'] = Mage::getModel('license/module')->getEncodeString($name_array);
			}
			
			if ($data['description']!='' || $data['description_default']==1) {
				$description_array = Mage::getModel('license/module')->getDecodeString($description);	
				if ($data['description_default']!=1) {
					$description_array[$storeId] = $data['description'];
				} else {
					unset($description_array[$storeId]);
				}
				$data['description'] = Mage::getModel('license/module')->getEncodeString($description_array);	
			}
			
			if ($data['website']!='' || $data['website_default']==1) {
				$website_array = Mage::getModel('license/module')->getDecodeString($website);	
				if ($data['website_default']!=1) {
					$website_array[$storeId] = $data['website'];
				} else {
					unset($website_array[$storeId]);
				}
				$data['website'] = Mage::getModel('license/module')->getEncodeString($website_array);	
			}
			
			if ($data['email']!='' || $data['email_default']==1) {
				$email_array = Mage::getModel('license/module')->getDecodeString($email);	
				if ($data['email_default']!=1) {
					$email_array[$storeId] = $data['email'];
				} else {
					unset($email_array[$storeId]);
				}
				$data['email'] = Mage::getModel('license/module')->getEncodeString($email_array);	
			}
				
			if ($data['facebook']!='' || $data['facebook_default']==1) {
				$facebook_array = Mage::getModel('license/module')->getDecodeString($facebook);	
				if ($data['facebook_default']!=1) {
					$facebook_array[$storeId] = $data['facebook'];
				} else {
					unset($facebook_array[$storeId]);
				}
				$data['facebook'] = Mage::getModel('license/module')->getEncodeString($facebook_array);	
			}
			
			if ($data['twitter']!='' || $data['twitter_default']==1) {
				$twitter_array = Mage::getModel('license/module')->getDecodeString($twitter);	
				if ($data['twitter_default']!=1) {
					$twitter_array[$storeId] = $data['twitter'];
				} else {
					unset($twitter_array[$storeId]);
				}
				$data['twitter'] = Mage::getModel('license/module')->getEncodeString($twitter_array);	
			}
			
			if ($data['phone']!='' || $data['phone_default']==1) {
				$phone_array = Mage::getModel('license/module')->getDecodeString($phone);	
				if ($data['phone_default']!=1) {
					$phone_array[$storeId] = $data['phone'];
				} else {
					unset($phone_array[$storeId]);
				}
				$data['phone'] = Mage::getModel('license/module')->getEncodeString($phone_array);	
			}
			
			if ($data['mobile']!='' || $data['mobile_default']==1) {
				$mobile_array = Mage::getModel('license/module')->getDecodeString($mobile);	
				if ($data['mobile_default']!=1) {
					$mobile_array[$storeId] = $data['mobile'];
				} else {
					unset($mobile_array[$storeId]);
				}
				$data['mobile'] = Mage::getModel('license/module')->getEncodeString($mobile_array);
			}
			
			if ($data['business_hours']!='' || $data['business_hours_default']==1) {
				$business_hours_array = Mage::getModel('license/module')->getDecodeString($business_hours);	
				if ($data['business_hours_default']!=1) {
					$business_hours_array[$storeId] = $data['business_hours'];
				} else {
					unset($business_hours_array[$storeId]);
				}
				$data['business_hours'] = Mage::getModel('license/module')->getEncodeString($business_hours_array);
			}
			
			if ($data['redeem']!='' || $data['redeem_default']==1) {
				$redeem_array = Mage::getModel('license/module')->getDecodeString($redeem);	
				if ($data['redeem_default']!=1) {
					$redeem_array[$storeId] = $data['redeem'];
				} else {
					unset($redeem_array[$storeId]);
				}
				$data['redeem'] = Mage::getModel('license/module')->getEncodeString($redeem_array);	
			}
						
			try {
				//upload/delete merchant logo	
				if(isset($_FILES['merchant_logo']['name']) && $_FILES['merchant_logo']['name']!='') {
					try {    
						 $uploader = new Varien_File_Uploader('merchant_logo');
						 $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
						 $uploader->setAllowRenameFiles(false);
						 $uploader->setFilesDispersion(false);
					 
						 $path = Mage::getBaseDir('media') . DS . 'groupdeals/merchants/logo/';
								
						 $uploader->save($path, $_FILES['merchant_logo']['name']);
					} catch (Exception $e) {
                		Mage::getSingleton('adminhtml/session')->addError($e->getMessage());						  
					}
					$data['merchant_logo'] = 'groupdeals/merchants/logo/'.$_FILES['merchant_logo']['name'];
				} elseif(isset($data['merchant_logo']['delete'])) {			
					$data['merchant_logo'] = '';			
				} else {
					unset($data['merchant_logo']);
				}							
				
				//create merchant's admin user/role/resources
            	$userModel = Mage::getModel('admin/user')->load($data['user_id']);
				if ($data['account']['username']!='' && !(!$userModel->getId() && $data['user_id']!='' && $data['user_id']!=0)) {	
					//set and encode permissions					
					$permissions = array();
					if (isset($data['account']['merchant_info'])) {
						$permissions['merchant_info'] = 1;
					} else {
						$permissions['merchant_info'] = 0;				
					}
					if (isset($data['account']['add_edit'])) {
						$permissions['add_edit'] = 1;
					} else {
						$permissions['add_edit'] = 0;				
					}
					if (isset($data['account']['approve'])) {
						$permissions['approve'] = 1;
					} else {
						$permissions['approve'] = 0;				
					}
					if (isset($data['account']['delete'])) {
						$permissions['delete'] = 1;
					} else {
						$permissions['delete'] = 0;				
					}
					if (isset($data['account']['sales'])) {
						$permissions['sales'] = 1;
					} else {
						$permissions['sales'] = 0;				
					}	
					$data['permissions'] = Mage::getModel('license/module')->getEncodeString($permissions);
															
					//verify if no password is added
					if (isset($data['account']['password']) && $data['account']['password']=='') {
						$data['account']['password_confirmation'] = '';					
						Mage::getSingleton('adminhtml/session')->setFormData($data);
						Mage::getSingleton('adminhtml/session')->addError('Please enter a Password.');
						$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id'), '_current' => true));			
						return;		
					}	
					
					//set user data
					if ($data['user_id']!=0) {
						$userData['user_id'] = $data['user_id'];
					}
					$userData['username'] = $data['account']['username'];
					
					if (isset($data['account']['password'])) {
						$userData['password'] = $data['account']['password'];
					}
					if (isset($data['account']['new_password'])) {
						$userData['new_password'] = $data['account']['new_password'];
					}
					$userData['password_confirmation'] = $data['account']['password_confirmation'];
					$userData['email'] = Mage::getModel('license/module')->getDecodeString($data['email'],0);
					$userData['firstname'] = 'merchant';
					$userData['lastname'] = 'merchant';
					if ($data['status']==1) { 
						$userData['is_active'] = $data['account']['is_active'];
					} else {
						$userData['is_active'] = 0;
					}
					$userModel->setData($userData);

					/*
					 * Unsetting new password and password confirmation if they are blank
					 */
					if ($userModel->hasNewPassword() && $userModel->getNewPassword() === '') {
						$userModel->unsNewPassword();
					}
					if ($userModel->hasPasswordConfirmation() && $userModel->getPasswordConfirmation() === '') {
						$userModel->unsPasswordConfirmation();
					}
					
					//validate user
					$result = $userModel->validate();
					if (is_array($result)) {
						Mage::getSingleton('adminhtml/session')->setFormData($data);
						foreach ($result as $message) {
							Mage::getSingleton('adminhtml/session')->addError($message);
						}
						$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id'), '_current' => true));
						return;
					}
					//save user
					$userModel->save();							
					
					//set user id for merchant
					$data['user_id'] = $userModel->getId();
    				
					//create user role				
					$userRole = Mage::getModel('admin/role')->getCollection()->addFieldToFilter('user_id', $data['user_id'])->getFirstItem();
					if ($userRole->getId()) {
						$role = Mage::getModel('admin/roles')->load($userRole->getParentId());
					} else {
						$role = Mage::getModel('admin/roles');
					}
					
					// preventing edit of relation role
        			if ($role->getId() && $role->getRoleType() != 'G') {
        			    $role->unsetData($role->getIdFieldName());
        			}
        			Mage::register('current_role', $role);

					$roleName = Mage::getModel('license/module')->getDecodeString($data['name'], 0);
					$role->setName($roleName.' Role')
						 ->setPid(false)
						 ->setRoleType('G') 						 
						 ->setGwsIsAll(1) 
						 ->save();
					
					//set resources to role
					$resources[] = false;
					if ($permissions['add_edit']==1 || $permissions['delete']==1 || $permissions['sales']==1) {
						$resources[] = 'admin/catalog/products';
						$resources[] = 'admin/groupdeals';
						$resources[] = 'admin/groupdeals/items';
					}
					
					if ($permissions['add_edit']==1) {
						$resources[] = 'admin/groupdeals/add';
						$resources[] = 'admin/cms/media_gallery';
					}

					if ($permissions['merchant_info']==1) {
						$resources[] = 'admin/groupdeals';
						$resources[] = 'admin/groupdeals/merchant_info';
					}
					
					Mage::getModel('admin/rules')
						->setRoleId($role->getId())
						->setResources($resources)
						->saveRel();
						
    				//add user to role    
					$userModel->setRoleId($role->getId())->setUserId($data['user_id']);
					if($userModel->roleUserExists() == false) {
						$userModel->add();
					}					
				} else if (!$userModel->getId() && $data['user_id']!='' && $data['user_id']!=0) {
					$data['permissions'] = '';	
					$data['user_id'] = '';			
                	Mage::getSingleton('adminhtml/session')->addError($this->__('This user no longer exists.'));
				}
				
				$model = Mage::getModel('groupdeals/merchants')
							->setData($data)
							->setId($this->getRequest()->getParam('id'))
							->save();	
				$merchantId = $model->getId();
				
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Merchant was successfully saved.'));

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $merchantId, '_current' => true));
					return;
				}
				$this->_redirect('*/*/', array('store'=>$storeId));
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $merchantId, '_current' => true));
                return;
            }
        }
        
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('groupdeals')->__('Unable to find merchant to save.'));
        $this->_redirect('*/*/', array('store'=>$storeId));
	}
 
	public function deleteAction() {
		if($merchantId = $this->getRequest()->getParam('id', false)) {
			$merchant = Mage::getModel('groupdeals/merchants')->load($merchantId);
			$user = Mage::getModel('admin/user')->load($merchant->getUserId());
			try {				
				//delete merchants admin account
				if ($user->getId()) {
					$roleId = Mage::getModel('admin/role')->load($user->getId(), 'user_id')->getParentId();
					$role = Mage::getModel('admin/roles')
							  ->setId($roleId)
							  ->delete();							  
					$user->delete();
				}				
				
				//delete merchant
				$merchant->delete();					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The merchant has been deleted.'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('_current' => true));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $merchantIds = $this->getRequest()->getParam('merchants');
        if(!is_array($merchantIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select merchant(s)'));
        } else {
            try {
                foreach ($merchantIds as $merchantId) {
                    $merchant = Mage::getModel('groupdeals/merchants')->load($merchantId);
                    $user = Mage::getModel('admin/user')->load($merchant->getUserId());
                    
                    //delete merchants admin account
					if ($user->getId()) {
						$roleId = Mage::getModel('admin/role')->load($user->getId(), 'user_id')->getParentId();
						$role = Mage::getModel('admin/roles')
								  ->setId($roleId)
								  ->delete();							  
						$user->delete();
					}				
					
					//delete merchant
					$merchant->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d merchant(s) were successfully deleted.', count($merchantIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $merchantIds = $this->getRequest()->getParam('merchants');
        if(!is_array($merchantIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select merchant(s)'));
        } else {
            try {
                foreach ($merchantIds as $merchantId) {
                    $merchant = Mage::getSingleton('groupdeals/merchants')
                        ->load($merchantId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                        
                    //disable merchant account if merchant disabled
                    if ($merchant->getUserId()!='' && $merchant->getUserId()!=0 && $merchant->getStatus()!=1) {
            			Mage::getModel('admin/user')->load($merchant->getUserId())->setIsActive(0)->save();
                    }
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d merchant(s) were successfully updated', count($merchantIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'merchants.csv';
        $content    = $this->getLayout()->createBlock('groupdeals/adminhtml_merchants_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, strip_tags($content));
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