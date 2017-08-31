<?php

/**
 * @package    Magedevgroup_TradeIn
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */
class Magedevgroup_TradeIn_Model_Resource_TradeInProposal extends Mage_Eav_Model_Entity_Abstract
{
    public function _construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('magedevgroup_tradein_tradeinproposal')
            ->setConnection(
                $resource->getConnection('magedevgroup_tradein_read'),
                $resource->getConnection('magedevgroup_tradein_write')
            );
    }

    protected function _getDefaultAttributes()
    {
        return array(
            'entity_type_id',
            'created_at',
            'updated_at',
            'increment_id',
            'store_id',
            'website_id'
        );
    }

    public function getMainTable()
    {
        return $this->getEntityTable();
    }
}
