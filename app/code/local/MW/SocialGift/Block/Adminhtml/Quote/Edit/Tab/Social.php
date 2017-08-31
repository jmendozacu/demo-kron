<?php
class MW_SocialGift_Block_Adminhtml_Quote_Edit_Tab_Social
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
        return Mage::helper('mw_socialgift')->__('Social Sharing');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('mw_socialgift')->__('Social Sharing');
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

    protected function _prepareForm()
    {
        $data = Mage::registry('current_socialgift_quote_rule');

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('rule_');
        $fieldset = $form->addFieldset('social_sharing_legend', array('legend'=>Mage::helper('mw_socialgift')->__('Social information')));

        $fieldset->addField('social_sharing','select',array(
            'label'     => Mage::helper('mw_socialgift')->__('Social Sharing'),
            'name'      =>'social_sharing',
            'values'    => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('mw_socialgift')->__('Enabled')
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('mw_socialgift')->__('Disabled')
                )
            )
        ));

        $fieldset->addField('google_plus','select',array(
            'label'     => Mage::helper('mw_socialgift')->__('Google Plus'),
            'name'      =>'google_plus',
            'values'    => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('mw_socialgift')->__('Yes')
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('mw_socialgift')->__('No')
                )
            )
        ));
        $fieldset->addField('facebook_like','select',array(
            'label'     => Mage::helper('mw_socialgift')->__('Facebook Like'),
            'name'      =>'facebook_like',
            'values'    => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('mw_socialgift')->__('Yes')
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('mw_socialgift')->__('No')
                )
            )
        ));
        $fieldset->addField('facebook_share','select',array(
            'label'     => Mage::helper('mw_socialgift')->__('Facebook Share'),
            'name'      =>'facebook_share',
            'values'    => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('mw_socialgift')->__('Yes')
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('mw_socialgift')->__('No')
                )
            )
        ));
        $fieldset->addField('twitter_tweet','select',array(
            'label'     => Mage::helper('mw_socialgift')->__('Twitter Tweet'),
            'name'      =>'twitter_tweet',
            'values'    => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('mw_socialgift')->__('Yes')
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('mw_socialgift')->__('No')
                )
            )
        ));

        $form->setValues($data->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
