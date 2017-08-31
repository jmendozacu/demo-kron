<?php
class MW_FreeGift_Block_Adminhtml_Catalog_Edit_Tab_Actions
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
	public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mw_freegift/freegift.phtml');
    }
    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('catalogrule')->__('Gift Items');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('catalogrule')->__('Gift Items');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('current_gift_catalog_rule');

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('action_fieldset', array('legend'=>Mage::helper('salesrule')->__('Update gift items using following information')));

        
       $fieldset->addField('stop_rules_processing', 'select', array(
            'label'     => Mage::helper('salesrule')->__('Stop Further Rules Processing'),
            'title'     => Mage::helper('salesrule')->__('Stop Further Rules Processing'),
            'name'      => 'stop_rules_processing',
            'options'    => array(
                '1' => Mage::helper('salesrule')->__('Yes'),
                '0' => Mage::helper('salesrule')->__('No'),
            ),
        ));
        
       $fieldset->addField('gift_product_ids', 'hidden', array(
            'name' => 'gift_product_ids',
            'required' => false,
            'label' => Mage::helper('salesrule')->__('Gift items'),
        ));
		
        
	    if ( Mage::registry('current_gift_catalog_rule')->getId() ) {
	          $form->setValues(Mage::registry('current_gift_catalog_rule')->getData());
	      }
        //$form->setUseContainer(true);

        if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }
		
        $this->setForm($form);

        return parent::_prepareForm();
    }
	
    protected function _prepareLayout()
    {
        $this->setChild('grid',
            $this->getLayout()->createBlock('freegift/adminhtml_catalog_edit_tab_actions_freegift','freegift_product_grid')
        );
        return parent::_prepareLayout();
    }
}
