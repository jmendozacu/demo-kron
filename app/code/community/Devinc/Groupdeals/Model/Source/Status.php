<?php

class Devinc_Groupdeals_Model_Source_Status extends Varien_Object
{
    const STATUS_QUEUED		= -1;
    const STATUS_RUNNING	= 1;
    const STATUS_DISABLED	= 2;
    const STATUS_ENDED  	= 3;
    const STATUS_PENDING	= 5;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_QUEUED     => Mage::helper('groupdeals')->__('Queued'),
            self::STATUS_RUNNING    => Mage::helper('groupdeals')->__('Running'),
            self::STATUS_ENDED      => Mage::helper('groupdeals')->__('Ended'),
            self::STATUS_DISABLED   => Mage::helper('groupdeals')->__('Disabled'),
            self::STATUS_PENDING    => Mage::helper('groupdeals')->__('Pending Approval'),
        );
    }
    
    static public function getAllOptions()
    {
        $res = array(
            array(
                'value' => '',
                'label' => Mage::helper('catalog')->__('-- Please Select --')
            )
        );
        foreach (self::getOptionArray() as $index => $value) {
            $res[] = array(
               'value' => $index,
               'label' => $value
            );
        }
        return $res;
    } 
    
    public function addValueSortToCollection($collection, $dir = 'asc')
    {
        if ($this->getAttribute()->isScopeGlobal()) {
            $tableName = $this->getAttribute()->getAttributeCode() . '_t';
            $collection->getSelect()
                ->joinLeft(
                    array($tableName => $this->getAttribute()->getBackend()->getTable()),
                    "`e`.`entity_id`=`{$tableName}`.`entity_id`"
                        . " AND `{$tableName}`.`attribute_id`='{$this->getAttribute()->getId()}'"
                        . " AND `{$tableName}`.`store_id`='0'",
                    array());
            $valueExpr = $tableName . '.value';
        }
        else {
            $valueTable1    = $this->getAttribute()->getAttributeCode() . '_t1';
            $valueTable2    = $this->getAttribute()->getAttributeCode() . '_t2';
            $collection->getSelect()
                ->joinLeft(
                    array($valueTable1 => $this->getAttribute()->getBackend()->getTable()),
                    "`e`.`entity_id`=`{$valueTable1}`.`entity_id`"
                        . " AND `{$valueTable1}`.`attribute_id`='{$this->getAttribute()->getId()}'"
                        . " AND `{$valueTable1}`.`store_id`='0'",
                    array())
                ->joinLeft(
                    array($valueTable2 => $this->getAttribute()->getBackend()->getTable()),
                    "`e`.`entity_id`=`{$valueTable2}`.`entity_id`"
                        . " AND `{$valueTable2}`.`attribute_id`='{$this->getAttribute()->getId()}'"
                        . " AND `{$valueTable2}`.`store_id`='{$collection->getStoreId()}'",
                    array()
                );
            $valueExpr = new Zend_Db_Expr("IF(`{$valueTable2}`.`value_id`>0, `{$valueTable2}`.`value`, `{$valueTable1}`.`value`)");
        }

        $collection->getSelect()->order($valueExpr . ' ' . $dir);
        return $this;
    }

    /**
     * Retrieve flat column definition
     *
     * @return array
     */
    public function getFlatColums()
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $column = array(
            'unsigned'  => true,
            'default'   => null,
            'extra'     => null
        );

        if (Mage::helper('core')->useDbCompatibleMode()) {
            $column['type']     = 'int';
            $column['is_null']  = true;
        } else {
            $column['type']     = Varien_Db_Ddl_Table::TYPE_INTEGER;
            $column['nullable'] = true;
            $column['comment']  = $attributeCode . ' groupdeals status column';
        }

        return array($attributeCode => $column);
    }

    /**
     * Retrieve Select for update attribute value in flat table
     *
     * @param   int $store
     * @return  Varien_Db_Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        return Mage::getResourceModel('eav/entity_attribute_option')
            ->getFlatUpdateSelect($this->getAttribute(), $store, false);
    }


}