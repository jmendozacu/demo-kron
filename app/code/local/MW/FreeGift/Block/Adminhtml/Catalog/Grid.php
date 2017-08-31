<?php

class MW_FreeGift_Block_Adminhtml_Catalog_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('freegiftGrid');
      $this->setDefaultSort('rule_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('freegift/rule')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

protected function _prepareColumns()
    {
        $this->addColumn('rule_id', array(
            'header'    => Mage::helper('catalogrule')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'rule_id',
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('catalogrule')->__('Rule Name'),
            'align'     =>'left',
            'index'     => 'name',
        ));

        $this->addColumn('from_date', array(
            'header'    => Mage::helper('catalogrule')->__('Date Start'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'index'     => 'from_date',
        ));

        $this->addColumn('to_date', array(
            'header'    => Mage::helper('catalogrule')->__('Date Expire'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'default'   => '--',
            'index'     => 'to_date',
        ));

        $this->addColumn('is_active', array(
            'header'    => Mage::helper('catalogrule')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
        		0 => 'Inactive',
                1 => 'Active'
            ),
        ));

        return parent::_prepareColumns();
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}