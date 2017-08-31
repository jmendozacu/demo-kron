<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Hideaddress
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Hideaddress Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Hideaddress
 * @author      Magestore Developer
 */
class Simi_Hideaddress_Block_Adminhtml_Hideaddress extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_hideaddress';
        $this->_blockGroup = 'hideaddress';
        $this->_headerText = Mage::helper('hideaddress')->__('Item Manager');
        $this->_addButtonLabel = Mage::helper('hideaddress')->__('Add Item');
        parent::__construct();
    }
}