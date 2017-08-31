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
 * @package     Mage_CatalogInventory
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml catalog inventory "Minimum Qty Allowed in Shopping Cart" field
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Sp_Pay4leter_Block_Adminhtml_Form_Field_Pay4laterplans extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * @var Mage_CatalogInventory_Block_Adminhtml_Form_Field_Customergroup
     */
    protected $_groupRenderer;

    /**
     * Retrieve group column renderer
     *
     * @return Mage_CatalogInventory_Block_Adminhtml_Form_Field_Customergroup
     */
    protected function _getGroupRenderer()
    {
        if (!$this->_groupRenderer) {
            $this->_groupRenderer = $this->getLayout()->createBlock(
                'cataloginventory/adminhtml_form_field_customergroup', '',
                array('is_render_to_js_template' => true)
            );
            $this->_groupRenderer->setClass('customer_group_select');
            $this->_groupRenderer->setExtraParams('style="width:120px"');
        }
        return $this->_groupRenderer;
    }

    /**
     * Prepare to render
     */
    protected function _prepareToRender()
    {
        $this->addColumn('plan_label', array(
            'label' => 'Plan Label',
            'style' => 'width:100px',
        ));
        $this->addColumn('plan_code', array(
            'label' => 'Plan cide',
            'style' => 'width:100px',
        ));
        $this->addColumn('deposite_1', array(
            'label' => 'Deposite 1',
            'style' => 'width:50px',
        ));
        $this->addColumn('deposite_2', array(
            'label' => 'Deposite 2',
            'style' => 'width:50px',
        ));
        $this->addColumn('deposite_3', array(
            'label' => 'Deposite 3',
            'style' => 'width:50px',
        ));
        $this->addColumn('deposite_4', array(
            'label' => 'Deposite 4',
            'style' => 'width:50px',
        ));
        $this->addColumn('deposite_5', array(
            'label' => 'Deposite 5',
            'style' => 'width:50px',
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = 'Add New Plan';
    }

    /**
     * Prepare existing row data object
     *
     * @param Varien_Object
     */
    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getGroupRenderer()->calcOptionHash($row->getData('customer_group_id')),
            'selected="selected"'
        );
    }
}
