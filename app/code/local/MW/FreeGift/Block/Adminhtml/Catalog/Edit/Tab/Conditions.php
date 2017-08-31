<?php
class MW_FreeGift_Block_Adminhtml_Catalog_Edit_Tab_Conditions
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
        return Mage::helper('catalogrule')->__('Conditions');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('catalogrule')->__('Conditions');
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
        $data = $model->getData();
        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/promo_catalog/newConditionHtml/form/rule_conditions_fieldset'));

        $fieldset = $form->addFieldset('conditions_fieldset', array(
            'legend'=>Mage::helper('catalogrule')->__('Conditions (leave blank for all products)'))
        )->setRenderer($renderer);
        if ($model->getId()) {
            $custom_cdn = unserialize($model->getConditionCustomized());

            if(isset($custom_cdn['buy_x_get_y'])){
                $data['buy_x'] = (isset($custom_cdn['buy_x_get_y']['bx'])) ? $custom_cdn['buy_x_get_y']['bx'] : 1;
                $data['get_y'] = (isset($custom_cdn['buy_x_get_y']['gy'])) ? $custom_cdn['buy_x_get_y']['gy'] : 1;
            }
        }
        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => Mage::helper('catalogrule')->__('Conditions'),
            'title' => Mage::helper('catalogrule')->__('Conditions'),
            'required' => true,
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/conditions'));

        $fieldset_condition = $form->addFieldset('base_fieldset_condition', array('legend'=>Mage::helper('catalogrule')->__('Apply when buy X get Y')));

        $fieldset_condition->addField('buy_x', 'text', array(
            'name' => 'buy_x',
            'label' => Mage::helper('catalogrule')->__('Minimum Quantity Required'),
            'style' => 'width: 80px;',
            /*'after_element_html' => '<small><i>X is quantity</i></small>',*/
        ));

        $fieldset_condition->addField('get_y', 'hidden', array(
            'name' => 'get_y',
            /*'label' => Mage::helper('catalogrule')->__('Y'),
            'style' => 'width: 80px;',
            'after_element_html' => '<small><i>Y is quantity</i></small>',*/
        ));
    	if ( Mage::registry('current_gift_catalog_rule')->getId() ) {
            $form->setValues($data);
	        /*$form->setValues(Mage::registry('current_gift_catalog_rule')->getData());*/
	      }

        //$form->setUseContainer(true);

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
