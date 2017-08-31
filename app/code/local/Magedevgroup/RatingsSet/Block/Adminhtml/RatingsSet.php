<?php
/**
 * @package    Magedevgroup_RatingsSet
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */

class Magedevgroup_RatingsSet_Block_Adminhtml_RatingsSet extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected function _construct()
    {
        parent::_construct();

        $this->_controller = 'ratingsSet';
        $this->_blockGroup = 'magedevgroup_ratingsset_adminhtml';
        $this->_headerText = Mage::helper('magedevgroup_ratingsset')->__('Set Manager');
        $this->_addButtonLabel = Mage::helper('magedevgroup_ratingsset')->__('Add Set');
    }

    public function getCreateUrl()
    {
        return $this->getUrl('*/*/edit');
    }
}