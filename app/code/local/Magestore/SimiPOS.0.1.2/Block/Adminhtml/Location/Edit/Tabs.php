<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Simipos Edit Tabs Block
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Block_Adminhtml_Location_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('simipos_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('simipos')->__('Location Information'));
    }
    
    /**
     * prepare before render block to html
     *
     * @return Magestore_SimiPOS_Block_Adminhtml_Location_Edit_Tabs
     */
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('simipos')->__('Location Information'),
            'title'     => Mage::helper('simipos')->__('Location Information'),
            'content'   => $this->getLayout()
                                ->createBlock('simipos/adminhtml_location_edit_tab_form')
                                ->toHtml(),
        ));
        
        $model = Mage::registry('location_data');
        if ($model->getId()) {
        	$this->addTab('user_section', array(
        	    'label'     => Mage::helper('simipos')->__('Sales Staff'),
                'title'     => Mage::helper('simipos')->__('Sales Staff'),
                'url'       => $this->getUrl('*/*/users',array(
                    '_current' => true, 'id' => $model->getId()
                )),
                'class'     => 'ajax',
        	));
        	
            $this->addTab('order_section', array(
                'label'     => Mage::helper('simipos')->__('Sales Orders'),
                'title'     => Mage::helper('simipos')->__('Sales Orders'),
                'url'       => $this->getUrl('*/*/orders',array(
                    '_current' => true, 'id' => $model->getId()
                )),
                'class'     => 'ajax',
            ));
        }
        
        return parent::_beforeToHtml();
    }
}
