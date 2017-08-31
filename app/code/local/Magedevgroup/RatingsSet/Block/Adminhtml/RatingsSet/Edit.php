<?php
/**
 * @package    Magedevgroup_RatingsSet
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */

class Magedevgroup_RatingsSet_Block_Adminhtml_RatingsSet_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected function _construct()
    {
        $this->_blockGroup = 'magedevgroup_ratingsset_adminhtml';
        $this->_controller = 'ratingsSet';

        /**
         * The $_mode property tells Magento which folder to use
         * to locate the related form blocks to be displayed in
         * this form container. In our example, this corresponds
         * to BrandDirectory/Block/Adminhtml/Brand/Edit/.
         */
        $this->_mode = 'edit';

        $newOrEdit = $this->getRequest()->getParam('id')
            ? $this->__('Edit')
            : $this->__('New');
        $this->_headerText = $newOrEdit . ' ' . $this->__('Set');
    }
}