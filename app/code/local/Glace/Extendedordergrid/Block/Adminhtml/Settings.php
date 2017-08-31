<?php
/*
 * Developer: Michael Jacky
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 */ 
    class Glace_Extendedordergrid_Block_Adminhtml_Settings extends Mage_Adminhtml_Block_Template{
        
        protected function _construct()
        {
            $this->setTemplate('ciextendedordergrid/settings.phtml');
        }

        protected function _prepareLayout()
        {
            $this->setChild('save_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label' => '<i class="fa fa-hand-o-down fa-2x"></i>'.Mage::helper('adminhtml')->__('Add Columns'),
                        'onclick' => 'varienAttributesForm.submit()',
                        'class' => 'save'
            )));


            return parent::_prepareLayout();
        }

        protected function getHeader()
        {
            return '<i class="fa fa-bolt fa-2x"></i>'.Mage::helper('ciextendedordergrid')->__('Manage Columns in Order Grid by Attributes');
        }

        protected function getSaveButtonHtml()
        {
            return $this->getChildHtml('save_button');
        }

        protected function getSaveFormAction()
        {
            return $this->getUrl('*/*/process');
        }
    }
?>