<?php
class Vivacity_Locator_Block_Adminhtml_Locator_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
   public function __construct()
   {
       parent::__construct();
       $this->setId('contactGrid');
       $this->setDefaultSort('locator_id');
       $this->setDefaultDir('ASC');
       $this->setSaveParametersInSession(true);
   }
   protected function _prepareCollection()
   {
      $collection = Mage::getModel('locator/locator')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
    }
   protected function _prepareColumns()
   {
    
       $this->addColumn('locator_id',
             array(
                    'header' => 'ID',
                    'align' =>'right',
                    'width' => '50px',
                    'index' => 'locator_id',
               ));
       $this->addColumn('store_name',
               array(
                    'header' => 'Store Name',
                    'align' =>'left',
                    'index' => 'store_name',
              ));
	$this->addColumn('store_image',
               array(
                    'header' => 'Store Image',
                    'align' =>'left',
                    'index' => 'store_image',
		    'renderer'  => 'locator/adminhtml_locator_renderer_image',
              ));
       $this->addColumn('address', array(
                    'header' => 'Store Address',
                    'align' =>'left',
                    'index' => 'address',
             ));
        $this->addColumn('city', array(
                     'header' => 'City',
                     'align' =>'left',
                     'index' => 'city',
          ));
$this->addColumn('zip_code', array(
                     'header' => 'Zip Code',
                     'align' =>'left',
                     'index' => 'zip_code',
          ));
$this->addColumn('country', array(
                     'header' => 'Country',
                     'align' =>'left',
                     'index' => 'country',
          ));
$this->addColumn('position', array(
                     'header' => 'Position',
                     'align' =>'left',
                     'index' => 'position',
          ));
$this->addColumn('status', array(
                     'header' => 'Status',
                     'align' =>'left',
                     'index' => 'status',
		     'type'=> 'options',
		     'options'=> array(1=>'Enabled',2=>'Disabled'),
          ));
$this->addExportType('*/*/exportCsv',Mage::helper('locator')->__('CSV'));
$this->addExportType('*/*/exportExcel',Mage::helper('locator')->__('EXCEL'));
         return parent::_prepareColumns();
    }
    public function getRowUrl($row)
    {
         return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}
