<?php

/**
 * @package    Magedevgroup_TradeIn
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */
class Magedevgroup_TradeIn_Block_Adminhtml_TradeIn_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _prepareCollection()
    {
        /** @var Magedevgroup_TradeIn_Model_Resource_TradeInProposal_Collection $collection */
        $collection = Mage::getModel('magedevgroup_tradein/tradeInProposal')
            ->getCollection();
        $collection->addAttributeToSelect('*');


        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn('entity_id',
            array(
                'header' => Mage::helper('tradein')->__('ID'),
                'index' => 'entity_id',
            ));
        $this->addColumn('date',
            array(
                'header' => Mage::helper('tradein')->__('Date'),
                'index' => 'created_at',
            ));
        $this->addColumn('fname',
            array(
                'header' => Mage::helper('tradein')->__('First Name'),
                'index' => 'fname',
            ));
        $this->addColumn('sname',
            array(
                'header' => Mage::helper('tradein')->__('Second Name'),
                'index' => 'sname',
            ));
        $this->addColumn('phone',
            array(
                'header' => Mage::helper('tradein')->__('Phone'),
                'index' => 'phone',
            ));
        $this->addColumn('mail',
            array(
                'header' => Mage::helper('tradein')->__('E-mail'),
                'index' => 'mail',
            ));
        $this->addColumn('brand',
            array(
                'header' => Mage::helper('tradein')->__('Brand'),
                'index' => 'brand',
            ));
        $this->addColumn('model',
            array(
                'header' => Mage::helper('tradein')->__('Model'),
                'index' => 'model',
            ));
        $this->addColumn('condition',
            array(
                'header' => Mage::helper('tradein')->__('Condition'),
                'index' => 'condition',
                'type'  => 'options',
                'options' => Mage::getModel('magedevgroup_tradein/entity_attribute_source_condition')->getOptionArray(),
            ));
        $this->addColumn('age',
            array(
                'header' => Mage::helper('tradein')->__('Age'),
                'index' => 'age',
            ));
        $this->addColumn('tradein_status',
            array(
                'header' => Mage::helper('tradein')->__('Status'),
                'index' => 'tradein_status',
                'type'  => 'options',
                'options' => Mage::getModel('magedevgroup_tradein/entity_attribute_source_status')->getOptionArray(),
            ));

        return $this;
    }

    public function getRowUrl($row)
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/tradein/edit', array(
                'store' => $this->getRequest()->getParam('store'),
                'id' => $row->getEntityId())
        );
    }
}
