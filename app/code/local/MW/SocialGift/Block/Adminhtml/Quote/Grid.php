<?php
class MW_SocialGift_Block_Adminhtml_Quote_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('socialgift_quote_grid'); //ID of our grid i.e the html id attribute of the <div>
        // This is the primary key of the database
        $this->setDefaultSort('sort_order');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(TRUE);
        // $this->setUseAjax(TRUE);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('mw_socialgift/salesrule')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('rule_id', array(
            'header'    => Mage::helper('mw_socialgift')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'rule_id',
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('mw_socialgift')->__('Rule Name'),
            'align'     =>'left',
            'index'     => 'name',
        ));

        $this->addColumn('from_date', array(
            'header'    => Mage::helper('mw_socialgift')->__('Date Start'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'index'     => 'from_date',
        ));

        $this->addColumn('to_date', array(
            'header'    => Mage::helper('mw_socialgift')->__('Date Expire'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'default'   => '--',
            'index'     => 'to_date',
        ));

        $this->addColumn('is_active', array(
            'header'    => Mage::helper('mw_socialgift')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                1 => 'Active',
                0 => 'Inactive',
            ),
        ));

        $this->addColumn('sort_order', array(
            'header'    => Mage::helper('mw_socialgift')->__('Priority'),
            'align'     => 'right',
            'index'     => 'sort_order',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getRuleId()));
    }

}
