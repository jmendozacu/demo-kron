<?php
class MW_FreeGift_Block_Adminhtml_Quote_Edit_Tab_Coupons_Form
    extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare coupon codes generation parameters form
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        /**
         * @var Mage_SalesRule_Helper_Coupon $couponHelper
         */
        $couponHelper = Mage::helper('freegift/coupon');

        $model = Mage::registry('current_freegift_quote_rule');
        $ruleId = $model->getId();

        $form->setHtmlIdPrefix('coupons_');

        $gridBlock = $this->getLayout()->getBlock('freegift_quote_edit_tab_coupons_grid');		
        $gridBlockJsObject = '';
        if ($gridBlock) {
            $gridBlockJsObject = $gridBlock->getJsObjectName();
        }

        $fieldset = $form->addFieldset('information_fieldset', array('legend'=>Mage::helper('salesrule')->__('Coupons Information')));
        $fieldset->addClass('ignore-validate');

        $fieldset->addField('rule_id', 'hidden', array(
            'name'     => 'rule_id',
            'value'    => $ruleId
        ));

        $fieldset->addField('qty', 'text', array(
            'name'     => 'qty',
            'label'    => Mage::helper('freegift')->__('Coupon Qty'),
            'title'    => Mage::helper('freegift')->__('Coupon Qty'),
            'required' => true,
            'class'    => 'validate-digits validate-greater-than-zero'
        ));

        $fieldset->addField('length', 'text', array(
            'name'     => 'length',
            'label'    => Mage::helper('freegift')->__('Code Length'),
            'title'    => Mage::helper('freegift')->__('Code Length'),
            'required' => true,
            'note'     => Mage::helper('freegift')->__('Excluding prefix, suffix and separators'),
            'value'    => $couponHelper->getDefaultLength(),
            'class'    => 'validate-digits validate-greater-than-zero'
        ));

        $fieldset->addField('format', 'select', array(
            'label'    => Mage::helper('freegift')->__('Code Format'),
            'name'     => 'format',
            'options'  => $couponHelper->getFormatsList(),
            'required' => true,
            'value'    => $couponHelper->getDefaultFormat()
        ));

        $fieldset->addField('prefix', 'text', array(
            'name'  => 'prefix',
            'label' => Mage::helper('freegift')->__('Code Prefix'),
            'title' => Mage::helper('freegift')->__('Code Prefix'),
            'value' => $couponHelper->getDefaultPrefix()
        ));

        $fieldset->addField('suffix', 'text', array(
            'name'  => 'suffix',
            'label' => Mage::helper('freegift')->__('Code Suffix'),
            'title' => Mage::helper('freegift')->__('Code Suffix'),
            'value' => $couponHelper->getDefaultSuffix()
        ));

        $fieldset->addField('dash', 'text', array(
            'name'  => 'dash',
            'label' => Mage::helper('freegift')->__('Dash Every X Characters'),
            'title' => Mage::helper('freegift')->__('Dash Every X Characters'),
            'note'  => Mage::helper('freegift')->__('If empty no separation'),
            'value' => $couponHelper->getDefaultDashInterval(),
            'class' => 'validate-digits'
        ));

        $idPrefix = $form->getHtmlIdPrefix();
        $generateUrl = $this->getGenerateUrl();

        $fieldset->addField('generate_button', 'note', array(
            'text' => $this->getButtonHtml(
                Mage::helper('freegift')->__('Generate'),
                "generateCouponCodes('{$idPrefix}' ,'{$generateUrl}', '{$gridBlockJsObject}')",
                'generate'
            )
        ));

        $this->setForm($form);

        Mage::dispatchEvent('adminhtml_promo_quote_edit_tab_coupons_form_prepare_form', array('form' => $form));

        return parent::_prepareForm();
    }

    /**
     * Retrieve URL to Generate Action
     *
     * @return string
     */
    public function getGenerateUrl()
    {
        return $this->getUrl('*/*/generate');
    }
}
