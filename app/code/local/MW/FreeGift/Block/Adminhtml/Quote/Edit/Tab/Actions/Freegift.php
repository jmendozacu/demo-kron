<?php
class MW_FreeGift_Block_Adminhtml_Quote_Edit_Tab_Actions_Freegift extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('freegift_product_grid');
        $this->setDefaultSort('entity_id');
        if($this->_getRule()->getId())
        	$this->setDefaultFilter(array('in_rules' => 1));
        $this->setUseAjax(true);
    }

    /**
     * Retirve currently edited product model
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getRule()
    {
        return Mage::registry('current_freegift_quote_rule');
    }

    /**
     * Add filter
     *
     * @param object $column
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Related
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_rules') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
            } else {
                if($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $productIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('*');

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Add columns to grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
            $this->addColumn('in_rules', array(
                'header_css_class'  => 'a-center',
                'type'              => 'checkbox',
                'name'              => 'in_rules',
                'values'            => $this->_getSelectedProducts(),
                'align'             => 'center',
                'index'             => 'entity_id',
            ));

        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('catalog')->__('ID'),
            'sortable'  => true,
            'width'     => 60,
            'index'     => 'entity_id'
        ));

        $this->addColumn('product_name', array(
            'header'    => Mage::helper('catalog')->__('Name'),
            'index'     => 'name'
        ));

        $this->addColumn('type', array(
            'header'    => Mage::helper('catalog')->__('Type'),
            'width'     => 100,
            'index'     => 'type_id',
            'type'      => 'options',
            'options'   => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name', array(
            'header'    => Mage::helper('catalog')->__('Attrib. Set Name'),
            'width'     => 130,
            'index'     => 'attribute_set_id',
            'type'      => 'options',
            'options'   => $sets,
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('catalog')->__('Status'),
            'width'     => 90,
            'index'     => 'status',
            'type'      => 'options',
            'options'   => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));

        $this->addColumn('visibility', array(
            'header'    => Mage::helper('catalog')->__('Visibility'),
            'width'     => 90,
            'index'     => 'visibility',
            'type'      => 'options',
            'options'   => Mage::getSingleton('catalog/product_visibility')->getOptionArray(),
        ));

        $this->addColumn('sku', array(
            'header'    => Mage::helper('catalog')->__('SKU'),
            'width'     => 80,
            'index'     => 'sku'
        ));

        $this->addColumn('price', array(
            'header'        => Mage::helper('catalog')->__('Price'),
            'type'          => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index'         => 'price'
        ));

		$this->addColumn('position', array(
            'header'            => Mage::helper('catalog')->__(''),
            'name'              => 'position',
            'type'              => 'hidden',
            'index'             => 'position',
            'width'             => 60,
            'editable'          => true,
            'edit_only'         => true,
			'sortable'			=> false,
			'style'				=> 'visible:hidden',
        ));
        return parent::_prepareColumns();
    }

    /**
     * Rerieve grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getData('grid_url')
            ? $this->getData('grid_url')
            : $this->getUrl('*/*/freeproduct', array('_current' => true));
    }

    /**
     * Retrieve selected related products
     *
     * @return array
     */
    protected function _getSelectedProducts()
    {
    	$products = $this->getFreeGifts();
    	if(!is_array($products)){
    		$products = explode(',',$this->_getRule()->getData('gift_product_ids'));
    	}
        return $products;
    }

    /**
     * Retrieve related products
     *
     * @return array
     */
    public function getSelectedRelatedProducts()
    {
        $products = array();
        
    }
}
