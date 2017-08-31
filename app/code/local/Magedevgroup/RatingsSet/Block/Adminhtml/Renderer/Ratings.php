<?php
/**
 * @package    Magedevgroup_RatingsSet
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */

class Magedevgroup_RatingsSet_Block_Adminhtml_Renderer_Ratings extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $value =  $row->getData($this->getColumn()->getIndex());

        $array = array();
        $ratings = Mage::getModel('rating/rating')
            ->getResourceCollection()
            ->addEntityFilter('product')
            ->addFieldToFilter('rating_id',  array('in'=> explode(',', $value)));

        foreach ($ratings as $rating) {
            $array[] =  $rating->getRatingCode();
        }
        return implode(', ', $array);
    }

}
?>