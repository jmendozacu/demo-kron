<?php 
class Simi_Themeone_Block_Adminhtml_Grid_Renderer_Spot_Storeimage extends Mage_Adminhtml_Block_Widget implements Varien_Data_Form_Element_Renderer_Interface
{
	protected $_element;
	protected $_image_type;
        protected $_image_type_id;
        protected $_phone_type;
        
	/**
	 * constructor
	*/
	public function __construct(){
       
		$this->setTemplate('themeone/renderer/spot/storeimage.phtml');
	}
	
	/*
	 * renderer
	*/
	public function render(Varien_Data_Form_Element_Abstract $element){
		$this->setElement($element);
		return $this->toHtml();
	}
        
        public function getRecommend()
        {
            if(($this->_image_type=="category" && ($this->_image_type_id ==2 or $this->_image_type_id==3))||$this->_phone_type=="tablet")
                    return $this->__('Recommended size: 708 x 354 px</br>'.'Base image is the main image for this category. Other images will slide in turn');
            else 
                return $this->__('Recommended size: 354 x 354 px</br>'.'Base image is the main image for this category. Other images will slide in turn');
        }
                /**
	 * get and set element
	*/
	public function setElement(Varien_Data_Form_Element_Abstract $element){
		$this->_element = $element;
		return $this;
	}
	public function getElement(){
		return $this->_element;
	}
	public function  getPhoneType(){
            return $this->_phone_type;
        }
        /*
	 * get value of element
	*/
	public function getValues(){
                return Mage::getModel('themeone/images')->getCollection()->addFieldToFilter('image_type', $this->_image_type)->addFieldToFilter('image_type_id', $this->_image_type_id)->addFieldToFilter('phone_type', $this->_phone_type)->addFieldToFilter('image_delete', 2);		
	}
	
        public function setType($image_type,$image_type_id,$phone_type){
            $this->_image_type=$image_type;
            $this->_image_type_id=$image_type_id;
            $this->_phone_type=$phone_type;
        }
        
        /*
	 * get button's html to show
	*/
	public function getAddButtonHtml(){
		$button = $this->getLayout()->createBlock('adminhtml/widget_button')
			->setData(array(
				'label'	=> $this->__('Add Image'),
				'onclick'	=> 'return '.$this->getElement()->getName().'Control.addItem()',
				'class'	=> 'add'
			));
		$button->setName('add_'.$this->getElement()->getName().'_button');
		$this->setChild('add_button',$button);
		return $this->getChildHtml('add_button');
	}
	
	
}