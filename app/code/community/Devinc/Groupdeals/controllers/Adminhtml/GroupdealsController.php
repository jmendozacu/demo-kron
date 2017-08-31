<?php
require_once 'Mage/Adminhtml/controllers/Catalog/ProductController.php';
class Devinc_Groupdeals_Adminhtml_GroupdealsController extends Mage_Adminhtml_Catalog_ProductController
{ 
	/**
     * Initialize groupdeals grid
     */
	public function indexAction() {		
		$this->loadLayout()
			->_setActiveMenu('groupdeals/items')
			->_title($this->__('Manage Deals'))
			->renderLayout();
	}	

    /**
     * groupdeals grid for ajax reload
     */
    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }
	
    /**
     * products grid for ajax reload when creating deal from catalog product
     */
    public function productsAction()
    {
		$this->getResponse()->setBody(
            $this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_tab_products')->toHtml()
        );		
    }
	
	/**
     * GROUPDEAL EDIT functions
     * Initialize groupdeal from request parameters
     */
    protected function _initGroupdeal()
    {
        $groupdealId = $this->getRequest()->getParam('groupdeals_id');
		$groupdeal = Mage::getModel('groupdeals/groupdeals');
		
		if ($groupdealId) {
			$groupdeal->load($groupdealId);
		}
				
		$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
		if (!empty($data)) {
			$groupdeal->setData($data);			
		}
		
        Mage::register('groupdeals_data', $groupdeal);
        return $groupdeal;
    }
 
	public function newAction() {
		$groupdeal = $this->_initGroupdeal();
		$product = $this->_initProduct();		

		$this->_title(false)->_title('New Deal');

        Mage::dispatchEvent('catalog_product_new_action', array('product' => $product));
		
		if ($this->getRequest()->getParam('popup')) {
            $this->loadLayout('popup');
        } else {
        	$_additionalLayoutPart = '';
            if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE
                && !($product->getTypeInstance()->getUsedProductAttributeIds()))
            {
                $_additionalLayoutPart = '_new';
            }
            $this->loadLayout(array(
                'default',
                strtolower($this->getFullActionName()),
                'groupdealsadmin_adminhtml_groupdeals_'.$product->getTypeId().$_additionalLayoutPart
            ));
			$this->_setActiveMenu('groupdeals/add');
        }		

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $block = $this->getLayout()->getBlock('catalog.wysiwyg.js');
        if ($block) {
            $block->setStoreId($product->getStoreId());
        }		
				
		$this->renderLayout();
	}
	
	public function editAction() {
        $groupdealId = $this->getRequest()->getParam('groupdeals_id');
		$groupdeal = $this->_initGroupdeal();        
		$productId = $this->getRequest()->getParam('id');
		$product = $this->_initProduct();

		if (($productId && !$product->getId()) || ($groupdealId && !$groupdeal->getId())) {
            $this->_getSession()->addError(Mage::helper('catalog')->__('This deal no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }
        
        if ($productId && $groupdealId && $groupdeal->getProductId()!=$product->getId()) {
            $this->_getSession()->addError(Mage::helper('catalog')->__('This deal no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }
        
        //if logged in as merchant redirect if not his deal
  	    if ($merchant = Mage::getModel('groupdeals/merchants')->isMerchant()) {
  	    	if ($merchant->getId() != $groupdeal->getMerchantId()) {
  	    		$this->_getSession()->addError(Mage::helper('catalog')->__('This deal no longer exists.'));
           		$this->_redirect('*/*/');
            	return;
  	    	}  	    
  	    }	

		$this->_title(false)->_title($product->getName());

        Mage::dispatchEvent('catalog_product_edit_action', array('product' => $product));

        $_additionalLayoutPart = '';
        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE
            && !($product->getTypeInstance()->getUsedProductAttributeIds()))
        {
            $_additionalLayoutPart = '_new';
        }
        
        $this->loadLayout(array(
            'default',
            strtolower($this->getFullActionName()),
            'groupdealsadmin_adminhtml_groupdeals_'.$product->getTypeId().$_additionalLayoutPart
        ));
		
		$this->_setActiveMenu('groupdeals/add');

        if (!Mage::app()->isSingleStoreMode() && ($switchBlock = $this->getLayout()->getBlock('store_switcher'))) {
            $switchBlock->setDefaultStoreName($this->__('Default Values'))
                ->setWebsiteIds($product->getWebsiteIds())
                ->setSwitchUrl(
                    $this->getUrl('*/*/*', array('_current'=>true, 'active_tab'=>null, 'tab' => null, 'store'=>null))
                );
        }

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $block = $this->getLayout()->getBlock('catalog.wysiwyg.js');
        if ($block) {
            $block->setStoreId($product->getStoreId());
        }		
				
		$this->renderLayout();
	}
	
    /**
     * save deal
     */
	public function saveAction() {
        $storeId = $this->getRequest()->getParam('store', 0);        
        $redirectBack   = $this->getRequest()->getParam('back', false);
		$productId = $this->getRequest()->getParam('id');
        $groupdealId = $this->getRequest()->getParam('groupdeals_id');
        
		if ($data = $this->getRequest()->getPost()) {	   
		     
	        $product = $this->_initProductSave();			
			try {				
				$product->save();    
                $productId = $product->getId();	
                
                //add description/short description
                $updateAttributes = array();
                $updateAttributes['description'] = $product->getGroupdealFineprint();
                $updateAttributes['short_description'] = $product->getGroupdealHighlights();	 		
				//if logged in as merchant and require administrators approval set deal status to pending
				$requireAdminApproval = Mage::getModel('groupdeals/merchants')->getPermission('approve');
  	    		if ($requireAdminApproval) {
  	   				$updateAttributes['status'] = 2;
  	   				$updateAttributes['groupdeal_status'] = Devinc_Groupdeals_Model_Source_Status::STATUS_PENDING;
				} else {
  	   				$updateAttributes['groupdeal_status'] = 0;		
				}               	
				Mage::getSingleton('catalog/product_action')->updateAttributes(array($product->getId()), $updateAttributes, $storeId);
				    			
                //do copying data to stores
                if (isset($data['copy_to_stores'])) {
                    foreach ($data['copy_to_stores'] as $storeTo=>$storeFrom) {
                        $newProduct = Mage::getModel('catalog/product')
                            ->setStoreId($storeFrom)
                            ->load($productId)
                            ->setStoreId($storeTo)
                            ->save();
                    }
                }	
                
				if (Mage::helper('groupdeals')->getMagentoVersion()>1420 && Mage::helper('groupdeals')->getMagentoVersion()<1800) {
                	Mage::getModel('catalogrule/rule')->applyAllRulesToProduct($productId);
				} else {
					$process = Mage::getModel('index/process')->load(3);
					$process->reindexEverything();
				}
				
				if ($this->getRequest()->getParam('groupdeals_id', false) || (!$this->getRequest()->getParam('groupdeals_id', false) && !$this->getRequest()->getParam('id', false))) {
					//update deal data
					$data['product_id'] = $productId;			
					
					//upload/delete barcode image				
					if(isset($_FILES['coupon_barcode']['name']) && $_FILES['coupon_barcode']['name']!='') {
						try {    
							 $uploader = new Varien_File_Uploader('coupon_barcode');
							 $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
							 $uploader->setAllowRenameFiles(false);
							 $uploader->setFilesDispersion(false);
						 
							 $path = Mage::getBaseDir('media') . DS . 'groupdeals/';
									
							 $uploader->save($path, $_FILES['coupon_barcode']['name']);
						} catch (Exception $e) {
               		 		Mage::getSingleton('adminhtml/session')->addError($e->getMessage());						  
						}
						$data['coupon_barcode'] = 'groupdeals/'.$_FILES['coupon_barcode']['name'];
					} elseif(isset($data['coupon_barcode']['delete'])) {			
						$data['coupon_barcode'] = '';			
					} else {
						unset($data['coupon_barcode']);
					}			
						
		            //set position default
		            if (!isset($data['position']) || $data['position']=='') {
						$data['position'] = 0;
					}	
							
					//format Expiration Date
					if (isset($data['coupon_expiration_date'])) {
						$data['coupon_expiration_date'] = Mage::getModel('eav/entity_attribute_backend_datetime')->formatDate($data['coupon_expiration_date']);  	
					}
								
					//save groupdeals data
					$groupdeal = Mage::getModel('groupdeals/groupdeals');	
					
					$groupdeal->setData($data)
						  ->setId($this->getRequest()->getParam('groupdeals_id'))
						  ->save();	
					$groupdealId = $groupdeal->getId();			
                        
					//add new country/states/cities info
					$crcIds = array();		
					$crcData['groupdeals_id'] = $groupdealId;	
					$crcData['product_id'] = $productId;
					for ($i=1; $i<=$data['region_city_nr']; $i++) {
						if (!isset($data['country_id']) || $data['country_id']=='') {
						    $crcData['country_id'] = '';
						    $crcData['region'] = '';
						    $crcData['city'] = 'Universal';						    
						} else {
						    $crcData['country_id'] = trim($data['country_id']);
							$crcData['region'] = trim($data['region'.$i]);
							$crcData['city'] = trim($data['city'.$i]);
						}	
						
						$crc = Mage::getSingleton('groupdeals/crc')->getCollection()->addFieldToFilter('groupdeals_id', $groupdealId)->addFieldToFilter('region', $crcData['region'])->addFieldToFilter('city', $crcData['city'])->getFirstItem();
						if (!$crc->getId()) {
							$crc = Mage::getModel('groupdeals/crc')->setData($crcData)->save();										
						}
						
						$crcIds[] = $crc->getId();
					}
					
					//delete country/states/cities info that isn't used anymore 
					$crcCollection = Mage::getSingleton('groupdeals/crc')->getCollection()->addFieldToFilter('groupdeals_id', $groupdealId)->addFieldToFilter('crc_id', array('nin' => $crcIds));
					if (count($crcCollection)) {
						foreach ($crcCollection as $crc) {
							$crc->delete();
						}		
					}
					
					//update url rewrites/refresh group deal's status
					Mage::getModel('groupdeals/groupdeals')->updateUrlRewrite($product, $groupdeal);
                	$newProductInstance = Mage::getModel('catalog/product')->setStoreId($storeId)->load($productId);	
		    		Mage::getModel('groupdeals/groupdeals')->refreshGroupdeal($newProductInstance);
				}
				
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Deal was successfully saved'));				
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage())->setFormData($data)->setProductData($data);	
                $redirectBack = true;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage())->setFormData($data)->setProductData($data);	
                $redirectBack = true;
            }
        }
        
        if ($redirectBack) {
            $this->_redirect('*/*/edit', array(
                'groupdeals_id' => $groupdealId,
                'id'   	 	    => $productId,
                '_current'	    =>true
            ));
        } elseif($this->getRequest()->getParam('popup')) {
            $this->_redirect('*/*/created', array(
                '_current'   	=> true,
                'groupdeals_id' => $groupdealId,
                'id'   	 	    => $productId,
            ));
        } else {
            $this->_redirect('*/*/', array('store'=>$storeId));
        }
	}

    //Create product duplicate when creating a deal from catalog product
    public function duplicateAction()
    {
        $product = $this->_initProduct();
        try {
            $newProduct = $product->duplicate();           
            
	        //add extra product info
	        $data = array();
	        $data['groupdeal_fineprint'] = $product->getDescription();
	        $data['groupdeal_highlights'] = $product->getShortDescription();
	        if ($product->getTypeId()=='configurable') {
	        	$data['sku'] = 'deal-configurable-'.rand(0, 9999);
	        }
	        $data['status'] = Mage_Catalog_Model_Product_Status::STATUS_ENABLED;
	        $data['stock_data'] = array();
	        $stockInfo = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->toArray();
	        $allowedStockKeys = array('use_config_manage_stock', 'original_inventory_qty', 'qty', 'use_config_min_qty', 'use_config_min_sale_qty', 'use_config_max_sale_qty', 'is_qty_decimal', 'use_config_backorders', 'use_config_notify_stock_qty', 'use_config_enable_qty_increments', 'use_config_qty_increments' ,'is_in_stock');
	        foreach($stockInfo as $key => $value) {
	        	if (in_array($key,$allowedStockKeys)) {
			        $data['stock_data'][$key] = $value;
			    }
	        }        
	        $attributeSetId = Mage::helper('groupdeals')->getGroupdealAttributeSetId();
	        $product = Mage::getModel('catalog/product')->load($newProduct->getId())->setAttributeSetId($attributeSetId)->addData($data)->save();
	        
	        //create group deal
	        $groupdealData = array();
	        $groupdealData['product_id'] = $product->getId();
	        $groupdealData['merchant_id'] = 0;
	        $groupdealData['minimum_qty'] = 0;
	        $groupdealData['maximum_qty'] = 1;
	        $groupdeal = Mage::getModel('groupdeals/groupdeals')->setData($groupdealData)->save();
	        
            $this->_redirect('*/*/edit', array('groupdeals_id'=>$groupdeal->getId(), 'id'=>$product->getId()));
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/');
        }
    }
		
	public function previewCouponAction() {		
		$coupon = Mage::getModel('groupdeals/coupons')->load($this->getRequest()->getParam('coupon_id'));
		$product = Mage::getModel('catalog/product')->load($this->getRequest()->getParam('id'));
		$groupdeal = Mage::getModel('groupdeals/groupdeals')->load($this->getRequest()->getParam('groupdeals_id'));
		
		if (!$product->getId() || !$groupdeal->getId()) {
            $this->_getSession()->addError(Mage::helper('catalog')->__('This coupon no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }        
        
        if ($coupon->getId()) {
			if ($coupon->getGroupdealsId()!=$groupdeal->getId()) {
        	    $this->_getSession()->addError(Mage::helper('catalog')->__('This coupon no longer exists.'));
        	    $this->_redirect('*/*/');
            	return;
       		}
        }
        
        //if logged in as merchant redirect if not his coupon
  	    if ($merchant = Mage::getModel('groupdeals/merchants')->isMerchant()) {
  	    	if ($merchant->getId() != $groupdeal->getMerchantId()) {
  	    		$this->_getSession()->addError(Mage::helper('catalog')->__('This coupon no longer exists.'));
           		$this->_redirect('*/*/');
            	return;
  	    	}  	    
  	    }
		
		$this->getResponse()->setBody(
            $this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_previewcoupon')->toHtml()
        );		
	}
 
	public function emailCouponsAction() {
		if($groupdealId = $this->getRequest()->getParam('groupdeals_id', false)) {
			try {
				$couponsCollection = Mage::getModel('groupdeals/coupons')->getCollection()->addFieldToFilter('groupdeals_id', $groupdealId)->addFieldToFilter('status', array('in' => array('pending', 'sending')));
				$itemsCollection = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('product_id', $this->getRequest()->getParam('id'));
			
				if (count($itemsCollection)>0) {
					if (count($couponsCollection)>0) {		
					    foreach ($couponsCollection as $coupon) {
					    	if ($coupon->getStatus()=='pending') {
						    	$coupon->setStatus('sending')->save();
						    }
					    }										
						Mage::getModel('groupdeals/coupons')->email();
						
						Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Coupons are being emailed.'));
					} else {
						Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Coupons have already been sent for all invoiced orders.'));
					}
				} else {
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('No orders were placed for this deal.'));	
				}

				$this->_redirect('*/*/edit', array('_current' => true));
				return;
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());					
				$this->_redirect('*/*/edit', array('_current' => true));
				return;
			}
		}
		
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('groupdeals')->__('Unable to find deal for which to send coupons'));
        $this->_redirect('*/*/');
	}
 
	public function emailMerchantAction() {
		if($groupdealId = $this->getRequest()->getParam('groupdeals_id', false)) {
			try {
				$groupdeal = $this->_initGroupdeal();	
				$merchant = Mage::getModel('groupdeals/merchants')->load($groupdeal->getMerchantId());
				$merchantEmail = Mage::getModel('license/module')->getDecodeString($merchant->getEmail(), 0);
				$product = $this->_initProduct();
				$productName = $product->getName();	
				$sender = Mage::getStoreConfig('groupdeals/configuration/coupons_sender');
				$replyTo = Mage::getStoreConfig('trans_email/ident_'.$sender.'/email');
			
				$couponsCollection = Mage::getModel('groupdeals/coupons')->getCollection()->addFieldToFilter('groupdeals_id', $groupdeal->getId())->addFieldToFilter('status', 'complete');
				if (count($couponsCollection)>0) {
					$content = $this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_tab_orders')->setIsCsv(true)->getCsv();					
					$emailData['name'] = $productName;
				
					$postObject = new Varien_Object();
					$postObject->setData($emailData);	
									
					$mailTemplate = Mage::getModel('core/email_template');						
					$this->addCsvAttachment($mailTemplate, $content, ''.$productName.' Coupons.csv');		
						
					$mailTemplate->setDesignConfig(array('area' => 'frontend'))
						->setReplyTo($replyTo)
						->sendTransactional(
							'groupdeals_notifications_email_merchant_template',
							$sender , 
							$merchantEmail,
							null,
							array('data' => $postObject)
						);						
					
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Coupons CSV Emailed.'));
				} else {
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('No coupons were generated for this deal.'));
				}

				$this->_redirect('*/*/edit', array('_current' => true));
				return;
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());					
				$this->_redirect('*/*/edit', array('_current' => true));
				return;
			}
		}
		
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('groupdeals')->__('Unable to find deal for which to send coupons to merchant.'));
        $this->_redirect('*/*/');
	}		
	
	public function deleteAction() {
		if($groupdealId = $this->getRequest()->getParam('groupdeals_id', false)) {
			$groupdeal = Mage::getModel('groupdeals/groupdeals')->load($groupdealId);
			$productId = $this->getRequest()->getParam('id');	
			$product = Mage::getModel('catalog/product')->load($productId);
			
			try {
                $groupdeal->delete();
                $product->delete();
                $this->_getSession()->addSuccess($this->__('The deal has been deleted.'));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
			    $this->_redirect('*/*/edit', array('_current' => true));
			    return;
            }			
		}
		
		$this->_redirect('*/*/', array('store'=>$this->getRequest()->getParam('store')));
	}
 
    /**
     * add csv attachment for email merchant action
     */
	public function addCsvAttachment($mailTemplate, $rFile, $sFilename) {
		$attachment = $mailTemplate->getMail()->createAttachment($rFile);
		$attachment->type = 'application/csv';
		$attachment->filename = $sFilename;
	}
	
    /**
     * generate region block for ajax updates
     */
	public function regionAction()
    {
		$this->getResponse()->setBody($this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals', 'region_field')->setTemplate('groupdeals/product/renderer/region.phtml')->toHtml());		
    }	
    
    //orders grid functions
    /**
     * Get orders grid
     */
    public function ordersAction()
    {
		$product = $this->_initProduct();
        $notice = '<div id="messages" class="notice"><ul class="messages"><li class="notice-msg"><ul><li><span>Coupons will only be sent to Invoiced Orders</span></li></ul></li></ul></div>';
		$this->getResponse()->setBody(
            $notice.$this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_tab_orders')->setIsCsv(false)->toHtml()
        );		
    }  
    
    /**
     * redeem coupon action
     */
	public function redeemAction()
    {
		$couponId = $this->getRequest()->getParam('coupon_id', false);
		$storeId = $this->getRequest()->getParam('store', 0);
		if ($couponId) {
			try {
				$coupon = Mage::getModel('groupdeals/coupons')
					->load($couponId)
					->setRedeem('used')
					->save();
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The "%s" Coupon has been redeemed.', $coupon->getCouponCode()));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find coupon to redeem.'));
		}
		$this->_redirect('*/*/edit', array('_current' => true, 'tab' => 'deal_tabs_orders'));
    }
    
    /**
     * email specific order's coupons
     */
	public function emailCouponAction() {
		if($orderId = $this->getRequest()->getParam('order_id',false)) {
			try {
				$order = Mage::getModel('sales/order')->load($orderId);
				if ($order->hasInvoices()) {
					$emailed = false;
					$items = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('order_id', $orderId)->addFieldToFilter('product_id', $this->getRequest()->getParam('id'));		
					
					if (count($items)>0) {
						foreach ($items as $item) {
							$couponsCollection = Mage::getModel('groupdeals/coupons')->getCollection()->addFieldToFilter('order_item_id', $item->getId())->addFieldToFilter('groupdeals_id', $this->getRequest()->getParam('groupdeals_id'));
							if (count($couponsCollection)>0) {		
							    foreach ($couponsCollection as $coupon) {
							    	if ($coupon->getStatus()!='voided') {
								    	$coupon->setStatus('sending')->save();
							    		$emailed = true;
								    }
							    }
							} 									
						}										
					}
						
					if ($emailed) {
					    Mage::getModel('groupdeals/coupons')->email();
					    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Coupon(s) have been emailed.'));
					} else {
					    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('No coupons were generated for this order.'));
					}
				} else {
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Order hasn\'t been invoiced.'));
				}
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
			$this->_redirect('*/*/edit', array('_current' => true, 'tab' => 'deal_tabs_orders'));
			return;
		}
		
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('groupdeals')->__('Unable to find order for which to send coupon(s).'));
		$this->_redirect('*/*/edit', array('_current' => true, 'tab' => 'deal_tabs_orders'));
	}	
  
    public function exportOrdersCsvAction()
    {
		$product = $this->_initProduct();
        $fileName   = $this->getRequest()->getParam('csv_excel_name').' Coupons.csv';
        $content    = $this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_tab_orders')->setIsCsv(true)
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }
	
    /**
     * Get notifications grid
     */
    public function notificationsAction()
    {
    	$this->_initGroupdeal();
    	$this->_initProduct();
		$this->getResponse()->setBody(
            $this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_tab_notifications')->toHtml()
        );		
    } 

    /**
     * GROUPDEALS GRID functions
     * deals grid edit action
     */
	public function columnEditAction() {
		$groupdealId = $this->getRequest()->getParam('groupdeals_id');
		$groupdeal = Mage::getModel('groupdeals/groupdeals')->load($groupdealId);
		$this->_redirect('*/*/edit', array('groupdeals_id' => $groupdealId, 'id' => $groupdeal->getProductId()));
	}
	
    public function massDeleteAction() 
    {
		$productIds = $this->getRequest()->getParam('product');
        if (!is_array($productIds)) {
            $this->_getSession()->addError($this->__('Please select product(s).'));
        } else {
            if (!empty($productIds)) {
                try {
                    foreach ($productIds as $productId) {
                        $groupdeals = Mage::getSingleton('groupdeals/groupdeals')->getCollection()->addFieldToFilter('product_id', $productId)->getFirstItem();
						$product = Mage::getSingleton('catalog/product')->load($productId);
                        Mage::dispatchEvent('catalog_controller_product_delete', array('product' => $product));
                        $groupdeals->delete();
                        $product->delete();
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) have been deleted.', count($productIds))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
		
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
		$productIds = (array)$this->getRequest()->getParam('product');
        $storeId    = (int)$this->getRequest()->getParam('store', 0);
        $status     = (int)$this->getRequest()->getParam('status');

        try {
            $this->_validateMassStatus($productIds, $status);
            Mage::getSingleton('catalog/product_action')
                ->updateAttributes($productIds, array('status' => $status), $storeId);

            $this->_getSession()->addSuccess(
                $this->__('Total of %d record(s) have been updated.', count($productIds))
            );
        }
        catch (Mage_Core_Model_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()
                ->addException($e, $this->__('An error occurred while updating the deal(s) status.'));
        }
		Mage::getModel('groupdeals/groupdeals')->refreshGroupdeals();
		
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'Groupdeals.csv';
        $content    = $this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_grid')
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