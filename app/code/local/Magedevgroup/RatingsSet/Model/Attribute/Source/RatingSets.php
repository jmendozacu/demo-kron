<?php
/**
 * @package    Magedevgroup_RatingsSet
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */

class Magedevgroup_RatingsSet_Model_Attribute_Source_RatingSets extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

    public function getAllOptions()
    {
        $collection = Mage::getModel('magedevgroup_ratingsset/set')->getCollection();
        foreach ($collection as $item){
            $this->_options[] = array(
                    'label' => $item->getName(),
                    'value' =>  $item->getEntityId()
                );
        }
        return $this->_options;
    }

    public function toOptionArray()
    {
        return $this->getAllOptions();
    }


    public function getFlatColums()
    {
        $columns = array(
            $this->getAttribute()->getAttributeCode() => array(
                'type'      => 'int',
                'unsigned'  => false,
                'is_null'   => true,
                'default'   => null,
                'extra'     => null
            )
        );
        return $columns;
    }


    public function getFlatUpdateSelect($store)
    {
        return Mage::getResourceModel('eav/entity_attribute')
            ->getFlatUpdateSelect($this->getAttribute(), $store);
    }
}