<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Ztheme
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Ztheme Grid Block
 * 
 * @category    Magestore
 * @package     Magestore_Ztheme
 * @author      Magestore Developer
 */
class Simi_Ztheme_Block_Adminhtml_Banner_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('bannerGrid');
        $this->setDefaultSort('banner_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }
    
    /**
     * prepare collection for block to display
     *
     * @return Simi_Ztheme_Block_Adminhtml_Ztheme_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('ztheme/banner')->getCollection();
        $cat = Mage::getModel('catalog/category');
        foreach($collection as $banner) {
            $cat->load($banner->getCategoryId());
            $banner->setData('category_name',$cat->getName());
        } 
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    /**
     * prepare columns for this grid
     *
     * @return Simi_Ztheme_Block_Adminhtml_Ztheme_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('banner_title', array(
            'header'    => Mage::helper('ztheme')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'banner_title',
        ));

        $this->addColumn('banner_title', array(
            'header'    => Mage::helper('ztheme')->__('Title'),
            'align'     =>'left',
            'index'     => 'banner_title',
        ));
        $this->addColumn('banner_position', array(
            'header'    => Mage::helper('ztheme')->__('Position'),
            'align'     =>'left',
            'index'     => 'banner_position',
        ));
        $this->addColumn('category_name', array(
            'header'    => Mage::helper('ztheme')->__('Category'),
            'index'     => 'category_name',
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('ztheme')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'status',
            'type'        => 'options',
            'options'     => array(
                1 => 'Enabled',
                2 => 'Disabled',
            ),
        ));

        $this->addColumn('action',
            array(
                'header'    =>    Mage::helper('ztheme')->__('Action'),
                'width'        => '100',
                'type'        => 'action',
                'getter'    => 'getId',
                'actions'    => array(
                    array(
                        'caption'    => Mage::helper('ztheme')->__('Edit'),
                        'url'        => array('base'=> '*/*/edit'),
                        'field'        => 'banner_id'
                    )),
                'filter'    => false,
                'sortable'    => false,
                'index'        => 'stores',
                'is_system'    => true,
        ));

       // $this->addExportType('*/*/exportCsv', Mage::helper('ztheme')->__('CSV'));
       // $this->addExportType('*/*/exportXml', Mage::helper('ztheme')->__('XML'));

        return parent::_prepareColumns();
    }
    
    /**
     * prepare mass action for this grid
     *
     * @return Simi_Ztheme_Block_Adminhtml_Ztheme_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('banner_id');
        $this->getMassactionBlock()->setFormFieldName('ztheme');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'        => Mage::helper('ztheme')->__('Delete'),
            'url'        => $this->getUrl('*/*/massDelete'),
            'confirm'    => Mage::helper('ztheme')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('ztheme/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
            'label'=> Mage::helper('ztheme')->__('Change status'),
            'url'    => $this->getUrl('*/*/massStatus', array('_current'=>true)),
            'additional' => array(
                'visibility' => array(
                    'name'    => 'status',
                    'type'    => 'select',
                    'class'    => 'required-entry',
                    'label'    => Mage::helper('ztheme')->__('Status'),
                    'values'=> $statuses
                ))
        ));
        return $this;
    }
    
    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('banner_id' => $row->getId()));
    }
}