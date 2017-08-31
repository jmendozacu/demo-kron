<?php

/**
 * @package    Magedevgroup_TradeIn
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */
class Magedevgroup_TradeIn_Block_Adminhtml_TradeIn_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected function _construct()
    {
        $this->_blockGroup = 'tradein_adminhtml';
        $this->_controller = 'tradeIn';

        /**
         * The $_mode property tells Magento which folder to use
         * to locate the related form blocks to be displayed in
         * this form container. In our example, this corresponds
         * to BrandDirectory/Block/Adminhtml/Brand/Edit/.
         */
        $this->_mode = 'edit';

        $this->_headerText = 'TradeIn Proposal';
    }

    protected function _prepareLayout()
    {
        $this->_removeButton('reset');
        $this->_updateButton("delete", "label", Mage::helper("tradein")->__("Decline Proposal"));

        $this->_addButton('accept_button',
            array(
                'label' => Mage::helper("tradein")->__("Accept Proposal"),
                'onclick' => 'confirmSetLocation(\'' . 'Are you sure you want accept this proposal?' . '\', \'' . $this->getUrl('adminhtml/tradein/accept',
                        array($this->_objectId => $this->getRequest()->getParam($this->_objectId))) . '\')'
            ));

        return parent::_prepareLayout();
    }

    public function getDeleteUrl()
    {
        return $this->getUrl('adminhtml/tradein/decline', array($this->_objectId => $this->getRequest()->getParam($this->_objectId)));
    }
}
