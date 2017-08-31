<?php
/*
 * Developer: Michael Jacky
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 */ 
class Glace_Extendedordergrid_Block_Adminhtml_Settings_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('ciextendedordergridsettings_tabs');
        $this->setDestElementId('attributesForm');
        $this->setTitle('<i class="fa fa-bolt fa-2x"></i>'.Mage::helper('ciextendedordergrid')->__('Add Columns To Orders Grid by Attributes'));
    }
    
    protected function _beforeToHtml()
    {
        $this->addTab('attributes_section', array(
            'label'     => '<i class="fa fa-thumbs-o-up fa-2x"></i>'.Mage::helper('ciextendedordergrid')->__('Attributes Configuration'),
            'title'     => Mage::helper('ciextendedordergrid')->__('Attributes Configuration'),
            'content'   => $this->getLayout()->createBlock('ciextendedordergrid/adminhtml_settings_tab_main')->toHtml(),
        ));
        
        return parent::_beforeToHtml();
    }
}
?>