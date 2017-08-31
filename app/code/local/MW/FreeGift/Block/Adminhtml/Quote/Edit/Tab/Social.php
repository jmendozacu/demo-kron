<?php
class MW_FreeGift_Block_Adminhtml_Quote_Edit_Tab_Social
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
        return Mage::helper('salesrule')->__('Social Sharing');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('salesrule')->__('Social Sharing');
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
        $data = Mage::registry('current_freegift_quote_rule');

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('rule_');
        $fieldset = $form->addFieldset('social_meta', array('legend'=>Mage::helper('freegift')->__('Social information')));

        $fieldset->addField('enable_social','select',array(
            'label'     => Mage::helper('freegift')->__('Social Sharing'),
            'name'      =>'enable_social',
            'values'    => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('freegift')->__('Enabled')
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('freegift')->__('Disabled')
                )
            )
        ));

        $fieldset->addField('google_plus','select',array(
            'label'     => Mage::helper('freegift')->__('Google Plus'),
            'name'      =>'google_plus',
            'values'    => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('freegift')->__('Yes')
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('freegift')->__('No')
                )
            )
        ));
        $fieldset->addField('like_fb','select',array(
            'label'     => Mage::helper('freegift')->__('Facebook Like'),
            'name'      =>'like_fb',
            'values'    => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('freegift')->__('Yes')
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('freegift')->__('No')
                )
            )
        ));
        $fieldset->addField('share_fb','select',array(
            'label'     => Mage::helper('freegift')->__('Facebook Share'),
            'name'      =>'share_fb',
            'values'    => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('freegift')->__('Yes')
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('freegift')->__('No')
                )
            )
        ));
        $fieldset->addField('twitter','select',array(
            'label'     => Mage::helper('freegift')->__('Twitter Tweet'),
            'name'      =>'twitter',
            'values'    => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('freegift')->__('Yes')
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('freegift')->__('No')
                )
            )
        ));

        $fieldset->addField('default_message', 'textarea', array(
            'label'     => Mage::helper('freegift')->__('Default Message'),
            'required'  => false,
            'name'      => 'default_message',
        ));

        $form->setValues($data->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
