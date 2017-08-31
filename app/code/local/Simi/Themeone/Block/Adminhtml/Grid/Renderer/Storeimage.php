<?php

class Simi_Themeone_Block_Adminhtml_Grid_Renderer_Storeimage
    extends Mage_Adminhtml_Block_Widget
    implements Varien_Data_Form_Element_Renderer_Interface
{
    protected $_element;
    protected $_image_type;
    protected $_image_type_id;

    /**
     * constructor
     */
    public function __construct()
    {

        $this->setTemplate('themeone/renderer/storeimage.phtml');
    }

    /**
     * renderer
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    public function getRecommend()
    {
        if ($this->_image_type == "category" && ($this->_image_type_id == 2 or $this->_image_type_id == 3))
            return $this->__('Recommended size: Width = 2 * Height' . 'Base image is the main image for this category. Other images will slide in turn');
        else
            return $this->__('Recommended size: Width =   Height' . 'Base image is the main image for this category. Other images will slide in turn');
    }

    /**
     * get and set element
     */
    public function setElement(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        return $this;
    }

    public function getElement()
    {
        return $this->_element;
    }

    /**
     * get value of element
     */
    public function getValues()
    {
        return Mage::getModel('themeone/images')
            ->getCollection()
            ->addFieldToFilter('image_type', $this->_image_type)
            ->addFieldToFilter('image_type_id', $this->_image_type_id)
            ->addFieldToFilter('image_delete', 2);
    }

    public function setType($image_type, $image_type_id)
    {
        $this->_image_type = $image_type;
        $this->_image_type_id = $image_type_id;
    }

    /**
     * get button's html to show
     */
    public function getAddButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label' => $this->__('Add Image'),
                'onclick' => 'return ' . $this->getElement()->getName() . 'Control.addItem()',
                'class' => 'add'
            ));
        $button->setName('add_' . $this->getElement()->getName() . '_button');
        $this->setChild('add_button', $button);
        return $this->getChildHtml('add_button');
    }
}
