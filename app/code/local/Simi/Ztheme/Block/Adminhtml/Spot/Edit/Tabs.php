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
 */
class Simi_Ztheme_Block_Adminhtml_Spot_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('ztheme_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('ztheme')->__('Spot Product Information'));
    }
    

    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('ztheme')->__('Spot Product Information'),
            'title'     => Mage::helper('ztheme')->__('Spot Product Information'),
            'content'   => $this->getLayout()
                                ->createBlock('ztheme/adminhtml_spot_edit_tab_form')
                                ->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
}