<?php

/**
 * @package    Magedevgroup_TradeIn
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */
class Magedevgroup_TradeIn_Block_Product_View_Form extends Varien_Data_Form_Element_Abstract
{

    public function getActionOnForm()
    {
        return $this->getUrl('tradein/index/createProposal');
	}
}
