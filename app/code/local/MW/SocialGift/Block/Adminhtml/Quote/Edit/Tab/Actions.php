<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * description
 *
 * @category    Mage
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class MW_SocialGift_Block_Adminhtml_Quote_Edit_Tab_Actions
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
        return Mage::helper('salesrule')->__('Actions');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('salesrule')->__('Actions');
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
        $model = Mage::registry('current_socialgift_quote_rule');

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('action_fieldset', array(
            'legend'=>Mage::helper('salesrule')->__('Update prices using the following information')
            )
        );

        $fieldset->addField('discount_amount', 'text', array(
            'name' => 'discount_amount',
            'required' => TRUE,
            'class' => 'validate-not-negative-number',
            'label' => Mage::helper('salesrule')->__('Discount Amount'),
        ));
        $model->setDiscountAmount( $model->getDiscountAmount() >= 0 ? $model->getDiscountAmount()*1 : '0' );

        $fieldset->addField('simple_action', 'select', array(
            'label'     => Mage::helper('catalogrule')->__('Apply'),
            'name'      => 'simple_action',
            'options'   => array(
                'by_percent'    => Mage::helper('catalogrule')->__('By Percentage of the Original Price'),
                'by_fixed'      => Mage::helper('catalogrule')->__('By Fixed Amount'),
                'to_percent'    => Mage::helper('catalogrule')->__('To Percentage of the Original Price'),
                'to_fixed'      => Mage::helper('catalogrule')->__('To Fixed Amount'),
            ),
        ));

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

}
