<?php
class MW_SocialGift_Block_Adminhtml_Quote_Edit_Tab_Gift
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('mw_socialgift')->__('Gift Items');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('mw_socialgift')->__('Gift Items');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return TRUE
     */
    public function canShowTab()
    {
        return TRUE;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return TRUE
     */
    public function isHidden()
    {
        return FALSE;
    }

	public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mw_socialgift/socialgift.phtml');
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('current_socialgift_quote_rule');

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('action_fieldset', array('legend'=>Mage::helper('salesrule')->__('Update gift items using following information')));

        $fieldset->addField('number_of_free_gift', 'text', array(
                'name' => 'number_of_free_gift',
                'label' => Mage::helper('mw_socialgift')->__('Number of Gift'),
                'title' => Mage::helper('mw_socialgift')->__('Number of Gift'),
            ));
        $model->setNumberOfFreeGift( $model->getNumberOfFreeGift() >= 0 ? $model->getNumberOfFreeGift() : '1' );

        $fieldset->addField('gift_product_ids', 'hidden', array(
            'name' 		=> 'gift_product_ids',
            'required' 	=> FALSE,
            'label' 	=> Mage::helper('mw_socialgift')->__('Gift Items'),
        ));

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

	protected function _prepareLayout()
    {
        $this->setChild('grid',
            $this->getLayout()->createBlock('mw_socialgift/adminhtml_quote_edit_tab_gift_socialgift','socialgift_product_grid')
        );
        return parent::_prepareLayout();
    }
}