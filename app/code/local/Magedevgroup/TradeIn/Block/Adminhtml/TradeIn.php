<?php

/**
 * @package    Magedevgroup_TradeIn
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */
class Magedevgroup_TradeIn_Block_Adminhtml_TradeIn extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'tradeIn';
        $this->_blockGroup = 'tradein_adminhtml';
        $this->_headerText = Mage::helper('tradein')->__('TradeIn Proposals');

        parent::__construct();
        $this->removeButton('add');
    }
}
