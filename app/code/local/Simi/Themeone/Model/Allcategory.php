<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of arraycategory
 *
 * @author thanhtung
 */
class Simi_Themeone_Model_Allcategory {

    protected $_options;

    public function toOptionArray($isMultiselect=false) {
        if (!$this->_options) {
            $this->_options = array();
            $categories = Mage::getModel('catalog/category')->getCollection()
                    ->addAttributeToSelect('entity_id')
                    ->addAttributeToSelect('name')
                    ->addAttributeToSelect('is_active');
           
            foreach ($categories as $category) {
                if ($category->getIsActive()) { // Only pull Active categories
                    $entity_id = $category->getId();
                    $name = $category->getName();
                    if($name==null || $name == "Root Catalog") continue;
                    $this->_options[]=array("value"=>$entity_id,
                        "label"=>$name);
                }
            }
        }

        $options = $this->_options;
        if (!$isMultiselect) {
            array_unshift($options, array('value' => '', 'label' => Mage::helper('adminhtml')->__('--Please Select--')));
        }
       usort($options,"cmpNameASC");
        return $options;
    }

}
function cmpNameASC($a, $b)
{
    return strcmp($a['label'], $b['label']);
}
function cmpNameDesc($a, $b)
{
    return -strcmp($a['label'], $b['label']);
}
?>
