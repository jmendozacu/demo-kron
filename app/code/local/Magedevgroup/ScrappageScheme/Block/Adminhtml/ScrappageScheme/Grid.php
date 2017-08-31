<?php

/**
 * @package    Magedevgroup_ScrappageScheme
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */
class Magedevgroup_ScrappageScheme_Block_Adminhtml_ScrappageScheme_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _prepareCollection()
    {
        /** @var Magedevgroup_ScrappageScheme_Model_Resource_Scrap_Collection $collection */
        $collection = Mage::getModel('scrappagescheme/scrap')
            ->getCollection();

        $collection->getSelect()->join(
            array('product' => Mage::getSingleton('core/resource')->getTableName('catalog/product')),
            'product.sku=main_table.sku',
            array('entity_id')
        );

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn('scrap_id',
            array(
                'header' => Mage::helper('scrappagescheme')->__('ID'),
                'width' => '50px',
                'type' => 'number',
                'index' => 'scrap_id',
            ));

        $this->addColumn('name',
            array(
                'header' => Mage::helper('scrappagescheme')->__('Name'),
                'index' => 'name',
            ));

        $this->addColumn('sku',
            array(
                'header' => Mage::helper('scrappagescheme')->__('SKU'),
                'width' => '80px',
                'index' => 'sku',
            ));

        $this->addExportType('*/*/exportCsvEmpty', Mage::helper('scrappagescheme')->__('Export empty'));
        $this->addExportType('*/*/exportCsvCurrent', Mage::helper('scrappagescheme')->__('Export current'));

        return $this;
    }

    public function getRowUrl($row)
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/catalog_product/edit/index/id', array(
                'store' => $this->getRequest()->getParam('store'),
                'id' => $row->getEntityId())
        );
    }
}
