<?php

/**
 * @package    Magedevgroup_ScrappageScheme
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */
class Magedevgroup_ScrappageScheme_Block_Adminhtml_ScrappageScheme extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'scrappageScheme';
        $this->_blockGroup = 'scrappagescheme_adminhtml';
        $this->_headerText = Mage::helper('scrappagescheme')->__('ScrappageScheme');

        parent::__construct();
        $this->removeButton('add');

        $this->_addButton('import', array(
            'label'     => 'Import',
            'onclick'   => '',
            'class'     => 'custom_import',
            'type'      => 'file',
        ));
    }

    protected function _addButtonChildBlock($childId)
    {
        if($childId == 'import_button') {
            $block = $this->getLayout()->createBlock('scrappagescheme_adminhtml/widget_button');
        }else{
            $block = $this->getLayout()->createBlock('adminhtml/widget_button');
        }
        $this->setChild($childId, $block);
        return $block;
    }

}
