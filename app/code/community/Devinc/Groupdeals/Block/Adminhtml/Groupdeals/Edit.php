<?php

class Devinc_Groupdeals_Block_Adminhtml_Groupdeals_Edit extends Mage_Adminhtml_Block_Widget
{
    protected $_formScripts = array();
    
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('groupdeals/product/edit.phtml');
        $this->setId('deal_edit');
    }
    
    protected function _prepareLayout()
    {               
    	//add groupdeal buttons
        if (!$this->getRequest()->getParam('popup')) {
            $this->setChild('back_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label'     => Mage::helper('catalog')->__('Back'),
                        'onclick'   => 'setLocation(\''.$this->getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store', 0))).'\')',
                        'class' 	=> 'back'
                    ))
            );
        } else {
            $this->setChild('back_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label'     => Mage::helper('catalog')->__('Close Window'),
                        'onclick'   => 'window.close()',
                        'class' 	=> 'cancel'
                    ))
            );
        }

        if (!$this->getProduct()->isReadonly() && $this->getRequest()->getParam('type', null)!='catalog') {
            $this->setChild('save_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label'     => Mage::helper('catalog')->__('Save'),
                        'onclick'   => 'dealForm.submit()',
                        'class' 	=> 'save'
                    ))
            );
        }

        if (!$this->getRequest()->getParam('popup')) {
            if (!$this->getProduct()->isReadonly() && $this->getRequest()->getParam('type', null)!='catalog') {
                $this->setChild('save_and_continue_button',
                    $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label'     => Mage::helper('catalog')->__('Save and Continue Edit'),
                            'onclick'   => 'saveAndContinueEdit(\''.$this->getSaveAndContinueUrl().'\')',
                            'class' 	=> 'save'
                        ))
                );
            }
            if ($this->getProduct()->isDeleteable()) {
                $this->setChild('delete_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label'     => Mage::helper('catalog')->__('Delete'),
                            'onclick'   => 'confirmSetLocation(\''.Mage::helper('catalog')->__('Are you sure?').'\', \''.$this->getDeleteUrl().'\')',
                            'class'  	=> 'delete'
                        ))
                );
            }
        }      
        
        if ($this->getRequest()->getParam('type', null)=='catalog') {
	        	$this->setChild('save_product_and_continue_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label'     => Mage::helper('catalog')->__('Save and Continue'),
                        'onclick'   => 'saveProductAndContinue(\''.$this->getSaveProductAndContinueUrl().'\')',
                        'class' 	=> 'save'
                    ))
            );
        }
		
		$groupdeal = $this->getGroupdeal();
		if ($groupdeal) {		
			// check deal's status
        	$storeId = $this->getRequest()->getParam('store', 0);
			if ($storeId==0) {
			    $storeIds = Mage::getModel('groupdeals/groupdeals')->getMainStoreIds(false);		
			} else {
			    $storeIds = array($storeId);
			}
			$isFinished = true;
			foreach ($storeIds as $store_id) 
			{	
			    $product = Mage::getModel('catalog/product')->setStoreId($store_id)->load($this->getProductId());
			    if ($product->getGroupdealStatus()!=Devinc_Groupdeals_Model_Source_Status::STATUS_DISABLED || $product->getGroupdealStatus()!=Devinc_Groupdeals_Model_Source_Status::STATUS_ENDED) {
			        $isFinished = false;
			    }
			}
			
			//get email coupons confirmation message
			$soldQty = Mage::getModel('groupdeals/groupdeals')->getGroupdealsSoldQty($groupdeal);	
			
			$emailCouponsMessage = 'Are you sure?';
			if ($isFinished) {
			    if ($soldQty>=$groupdeal->getMinimumQty()) {
			    	$emailCouponsMessage = 'Are you sure you want to email the coupons to your customers?';
			    } else {
			    	$emailCouponsMessage = 'The target was not met! Are you sure you want to email the coupons to your customers?';					
			    }
			} else {
			    if ($soldQty>=$groupdeal->getMinimumQty()) {	
			    	$emailCouponsMessage = 'The deal has not ended yet on all of your stores. Are you sure you want to email the coupons to your customers?';
			    } else {
			    	$emailCouponsMessage = 'The target was not met and the deal has not ended yet on all of your stores. Are you sure you want to email the coupons to your customers?';
			    }			
			}
			
			//add virtual groupdeal buttons
			$this->setChild('email_coupons_button',
        	$this->getLayout()->createBlock('adminhtml/widget_button')
        	        ->setData(array(
        	            'label'     => Mage::helper('catalog')->__('Email Coupons'),
        	            'onclick'   => 'confirmSetLocation(\''.Mage::helper('catalog')->__($emailCouponsMessage).'\', \''.$this->getEmailCouponsUrl().'\')',
        	        ))
        	);
        	
			$this->setChild('email_merchant_button',
        	$this->getLayout()->createBlock('adminhtml/widget_button')
        	        ->setData(array(
        	            'label'     => Mage::helper('catalog')->__('Email Coupons CSV to Merchant'),
        	            'onclick'   => 'confirmSetLocation(\''.Mage::helper('catalog')->__('Are you sure?').'\', \''.$this->getEmailMerchantUrl().'\')',
        	        ))
        	);
        	
			$this->setChild('preview_coupon_button',
        	$this->getLayout()->createBlock('adminhtml/widget_button')
        	        ->setData(array(
        	            'label'     => Mage::helper('catalog')->__('Preview Coupon'),
        	            'onclick'   => 'window.open(\''.$this->getPreviewCouponUrl().'\', \'\', \'width=715,height=1000,resizable=1,scrollbars=1\')',
        	        ))
        	);  
        	
			//if (Mage::app()->getRequest()->getActionName()!='new') {		
			$previewDealButton = $product->getProductUrl();
			if (strpos($previewDealButton, '?') === false) {
			    $previewDealButton .= '?deal_preview=1';
			} else {
			    $previewDealButton .= '&deal_preview=1';
			}
			$this->setChild('preview_deal_button',
	        $this->getLayout()->createBlock('adminhtml/widget_button')
	                ->setData(array(
	                    'label'     => Mage::helper('catalog')->__('Preview Deal'),
	                    'onclick'   => 'window.open(\''.$previewDealButton.'\')',
	                ))
	        ); 
	        //}
        }  

        return parent::_prepareLayout();
    }

	//button functions
    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    public function getCancelButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    public function getEmailCouponsButtonHtml()
    {
        return $this->getChildHtml('email_coupons_button');
    }

    public function getEmailMerchantButtonHtml()
    {
        return $this->getChildHtml('email_merchant_button');
    }

    public function getPreviewCouponButtonHtml()
    {
        return $this->getChildHtml('preview_coupon_button');
    }

    public function getPreviewDealButtonHtml()
    {
        return $this->getChildHtml('preview_deal_button');
    }

    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    public function getSaveAndContinueButtonHtml()
    {
        return $this->getChildHtml('save_and_continue_button');
    }

    public function getSaveProductAndContinueButtonHtml()
    {
        return $this->getChildHtml('save_product_and_continue_button');
    }
    
    //url functions
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array('_current'=>true));
    }
    
    public function getEmailCouponsUrl()
    {
        return $this->getUrl('*/*/emailCoupons', array('_current'=>true));
    }
    
    public function getEmailMerchantUrl()
    {
        return $this->getUrl('*/*/emailMerchant', array('_current'=>true));
    }
    
    public function getPreviewCouponUrl()
    {
        return $this->getUrl('*/*/previewCoupon', array('_current'=>true));
    }

    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }
    
    public function getSaveUrl()
    {  
        return $this->getUrl('*/*/save', array('_current'=>true, 'back'=>null));	
    }
    
    public function getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
	        '_current'   => true,         	
	        'back'       => 'edit',
	        'tab'        => '{{tab_id}}',
	        'active_tab' => null
	    ));	 
    }
    
    public function getSaveProductAndContinueUrl()
    {
        return $this->getUrl('*/*/duplicate', array(
            'id' => '{{product_id}}'
        ));
    }
    
    //main functions	
	public function getGroupdeal()
    {
    	if ($this->getRequest()->getParam('groupdeals_id', false)) {
	        return Mage::getModel('groupdeals/groupdeals')->load($this->getRequest()->getParam('groupdeals_id'));
	    }
	    
	    return false;
    }
    
	public function getProduct()
    {
        return Mage::registry('current_product');
    }
	
	public function getProductId()
    {
        return $this->getProduct()->getId();
    }

    public function getProductSetId()
    {
        $setId = false;
        if (!($setId = $this->getProduct()->getAttributeSetId()) && $this->getRequest()) {
            $setId = $this->getRequest()->getParam('set', null);
        }
        return $setId;
    }
    
    public function getIsVirtual()
    {
        return $this->getProduct()->isVirtual();
    }    

    public function getIsConfigured()
    {
        if ($this->getProduct()->isConfigurable()
            && !($superAttributes = $this->getProduct()->getTypeInstance(true)->getUsedProductAttributeIds($this->getProduct()))) {
            $superAttributes = false;
        }

        return !$this->getProduct()->isConfigurable() || $superAttributes !== false;
    }
    
    public function getMerchantPermission($_permission) {	
    	$allow = Mage::getModel('groupdeals/merchants')->getPermission($_permission);	
		
		return $allow;
    }
    
    public function getHeader()
    {
        $header = '';
        if ($this->getProduct()->getId()) {
            $header = $this->htmlEscape($this->getProduct()->getName());
        }
        else {
            $header = Mage::helper('catalog')->__('New Deal');
        }
        if ($setName = $this->getAttributeSetName()) {
            $header.= ' (' . $setName . ')';
        }
        return $header;
    }

    public function getAttributeSetName()
    {
        if ($setId = $this->getProduct()->getAttributeSetId()) {
            $set = Mage::getModel('eav/entity_attribute_set')
                ->load($setId);
            return $set->getAttributeSetName();
        }
        return '';
    }
    
    public function getFormScripts()
    {
        if ( !empty($this->_formScripts) && is_array($this->_formScripts) ) {
            return '<script type="text/javascript">' . implode("\n", $this->_formScripts) . '</script>';
        }
        return '';
    }
    
    public function getSelectedTabId()
    {
        return addslashes(htmlspecialchars($this->getRequest()->getParam('tab')));
    }
}