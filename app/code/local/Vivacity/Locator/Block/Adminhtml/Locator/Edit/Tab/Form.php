<?php
class Vivacity_Locator_Block_Adminhtml_Locator_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
   protected function _prepareForm()
   {
       $form = new Varien_Data_Form();
       $this->setForm($form);
       $fieldset = $form->addFieldset('locator_form',array('legend'=>'Fill Store Locator Information','enctype'=>'multipart/form-data'));
	$fieldset->addType('image', 'Vivacity_Locator_Block_Adminhtml_Entity_Helper_Image');
	
        $fieldset->addField('store_name', 'text',
                       array(
                          'label' => 'Store Name',
                          'class' => 'required-entry',
                          'required' => true,
                           'name' => 'store_name',
                    ));
        $fieldset->addField('address', 'text',
                         array(
                          'label' => 'Address',
                          'class' => 'required-entry',
                          'required' => true,
                          'name' => 'address',
                      ));
          $fieldset->addField('city', 'text',
                    array(
                        'label' => 'City',
                        'class' => 'required-entry',
                        'required' => true,
                        'name' => 'city',
                 ));
	$fieldset->addField('zip_code', 'text',
                    array(
                        'label' => 'Zip Code',
                        'class' => 'required-entry',
                        'required' => true,
                        'name' => 'zip_code',
                 ));
	$fieldset->addField('country', 'text',
                    array(
                        'label' => 'Country',
                        'class' => 'required-entry',
                        'required' => true,
                        'name' => 'country',
                 ));
	$fieldset->addField('state', 'text',
                    array(
                        'label' => 'State',
                        'class' => 'required-entry',
                        'required' => true,
                        'name' => 'state',

                 ));
	$fieldset->addField('faxno', 'text',
                    array(
                        'label' => 'Fax Number',
                        'required' => false,
                        'name' => 'faxno',
                 ));
	$fieldset->addField('phone', 'text',
                    array(
                        'label' => 'Phone number',
                        'required' => false,
                        'name' => 'phone',
                 ));
	$fieldset->addField('email', 'text',
                    array(
                        'label' => 'Email-address',
                        'required' => false,
                        'name' => 'email',
                 ));
	$fieldset->addField('status', 'select',
                    array(
                        'label' => 'Status',
                        'class' => 'required-entry',
                        'required' => true,
                        'name' => 'status',
			'values' => array('-1'=>'Please Select Status','1' => 'Enable','2' => 'Disable'),
			'onchange'   => 'getLatNLong()',
          		'disabled' => false,
			
                 ));
	$fieldset->addField('description', 'textarea',
                    array(
                        'label' => 'Description',
                        'class' => 'required-entry',
                        'required' => true,
                        'name' => 'description',
                 ));
	$fieldset->addField('custom_icon', 'image',
                    array(
                        'label' => 'Custom Store Icon',
                        'class' => 'required-entry',
                        'required' => true,
                        'name' => 'custom_icon',
			'readonly' => true,
          		'after_element_html' => '<small>Please upload 40 * 40 size image</small>',
                 ));
	$fieldset->addField('position', 'text',
                    array(
                        'label' => 'Set order',
                        'name' => 'position',
                 ));
	$fieldset->addField('store_image', 'image',
                    array(
                        'label' => 'Store image',
                        'class' => 'required-entry',
                        'required' => true,
                        'name' => 'store_image',
			'readonly' => true,
          		'after_element_html' => '<small>Please upload 80 * 80 size image</small>',
                 ));
$fieldset->addField('hideGoogle', 'hidden',
                    array(
                        'label' => 'hideGoogle',
                        'name'  => 'hideGoogle',
			'value' => 0,
                 ));
	$fieldset->addField('lat', 'text',
                    array(
                        'label' => 'Latitude',
                        'class' => 'required-entry',
                        'required' => true,
                        'name' => 'lat',
                 ));
	$fieldset->addField('long', 'text',
                    array(
                        'label' => 'Longitude',
                        'class' => 'required-entry',
                        'required' => true,
                        'name' => 'long',

                 ))->setAfterElementHtml("<style>
#map_wrapper {
    height: 400px;
    width:500px;
}

#map_canvas {
    width: 100%;
    height: 100%;
}
    </style></script><div id='map_wrapper' style='margin-top:25px;display:none;'><div id='map_canvas' class='mapping'></div></div><script type=\"text/javascript\" src='https://maps.googleapis.com/maps/api/js?v=3.exp' ></script><script type=\"text/javascript\" src='".Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS)."locator/adminGoogle.js'></script>");
 if ( Mage::registry('locator_data') )
 {
    $form->setValues(Mage::registry('locator_data')->getData());
  }
  return parent::_prepareForm();
 }
}
