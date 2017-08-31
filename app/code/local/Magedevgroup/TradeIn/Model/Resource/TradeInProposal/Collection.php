<?php
/**
 * @package    Magedevgroup_TradeIn
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */

/**
 * Class Magedevgroup_TradeIn_Model_Resource_TradeInProposal_Collection
 *
 * @method string getCreatedAt
 */
class  Magedevgroup_TradeIn_Model_Resource_TradeInProposal_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('magedevgroup_tradein/tradeInProposal');
    }
}
