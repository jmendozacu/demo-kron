<?php
/**
 * @package    Magedevgroup_RatingsSet
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */


class Magedevgroup_RatingsSet_Block_Adminhtml_RatingsSet_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     * Dvgfgfg.
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        /** @var Magedevgroup_RatingsSet_Model_Resource_Set_Collection $collection */
        $collection = Mage::getModel('magedevgroup_ratingsset/set')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Sfddsfsfd.
     *
     * @param  $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            array(
                'header' => $this->_getHelper()->__('ID'),
                'type' => 'number',
                'index' => 'entity_id',
            )
        );

        $this->addColumn(
            'name',
            array(
                'header' => $this->_getHelper()->__('Name'),
                'type' => 'text',
                'index' => 'name',
            )
        );
        $this->addColumn(
            'ratings',
            array(
                'header' => $this->_getHelper()->__('Ratings'),
                'type' => 'text',
                'index' => 'ratings',
                'renderer'  => 'Magedevgroup_RatingsSet_Block_Adminhtml_Renderer_Ratings'
            )
        );
        $this->addColumn(
            'action',
            array(
                'header' => $this->_getHelper()->__('Action'),
                'width' => '50px',
                'type' => 'action',
                'actions' => array(
                    array(
                        'caption' => $this->_getHelper()->__('Edit'),
                        'url' => array(
                            'base' => '*'
                                . '/*/edit',
                        ),
                        'field' => 'id'
                    ),
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'entity_id',
            )
        );

        return parent::_prepareColumns();
    }

    /**
     * @return Mage_Core_Helper_Abstract
     */
    private function _getHelper()
    {
        return Mage::helper('magedevgroup_ratingsset');
    }
}