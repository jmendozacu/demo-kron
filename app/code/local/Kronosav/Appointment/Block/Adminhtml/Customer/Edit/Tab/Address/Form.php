<?php 
class Kronosav_Appointment_Block_Adminhtml_Customer_Edit_Tab_Address_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		
		    $form = new Varien_Data_Form(array(
             'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' =>$this->getRequest()->getParam('id'))),
            'method' => 'post',
            'enctype' => 'multipart/form-data'));
		$this->setForm($form);
		$fieldset = $form->addFieldset('appointment_form', array('legend'=>Mage::helper('appointment')->__('Appointment Information')));
		
	
		
		if(Mage::registry('customeredit_data'))
		{
		    
			$fieldset->addField('entity_id', 'hidden', array(
			'label'     => Mage::helper('appointment')->__('Entity ID'),
			'class'     => 'required-entry',
			'required'  => true,
			//'readonly' => true,
			'name'      => 'entity_id',
			'value'	=>$entityid,
		));
		
		}	
       	
		$fieldset->addField('company', 'text', array(
			'label'     => Mage::helper('appointment')->__('Company'),
			'class'     => 'required-entry',
			//'required'  => true,
			'name'      => 'company',
		));			
		$fieldset->addField('street_address', 'text', array(
			'label'     => Mage::helper('appointment')->__('Street Address'),
			'class'     => 'required-entry',
			//'required'  => true,
			'name'      => 'streetaddress',
		));	
		$fieldset->addField('city', 'text', array(
			'label'     => Mage::helper('appointment')->__('City'),
			'class'     => 'required-entry',
			//'required'  => true,
			'name'      => 'city',
		));
		
		$country = $fieldset->addField('country_id', 'select', array(
        'name'  => 'country_id',
        'label' => 'Country',
        'values'    => Mage::getModel('adminhtml/system_config_source_country')->toOptionArray(),
		'onchange' => 'getstate(this)',
		));
		
		
		$fieldset->addField('state', 'note', array(
			'text'     => Mage::getModel('appointment/appointment')->getstate('AU'),
			'name'      => 'state',
			'label'      => 'State',
        ));
		
        $fieldset->addField('zipcode', 'text', array(
			'label'     => Mage::helper('appointment')->__('Zip Code'),
			'class'     => 'required-entry',
			//'required'  => true,
			'name'      => 'zipcode',
		));
		$fieldset->addField('telephone', 'text', array(
			'label'     => Mage::helper('appointment')->__('Telephone'),
			'class'     => 'required-entry',
			//'required'  => true,
			'name'      => 'telephone',
		));
		$fieldset->addField('fax', 'text', array(
			'label'     => Mage::helper('appointment')->__('Fax'),
			//'class'     => 'required-entry',
			//'required'  => true,
			'name'      => 'fax',
		));
		$fieldset->addField('vat_no', 'text', array(
			'label'     => Mage::helper('appointment')->__('VAT Number'),
			'class'     => 'required-entry',
			//'required'  => true,
			'name'      => 'vatno',
		));
		
		$country->setAfterElementHtml("<script type=\"text/javascript\">
            function getstate(selectElement){
				
                var reloadurl = '". $this
                 ->getUrl('appointment/adminhtml_appointment/state') . "country/' + selectElement.value;
				
                new Ajax.Request(reloadurl, {
                    method: 'get',
                    onLoading: function (edit_form) {
                        document.getElementById('state').innerHTML = 'Loading..';
                    },
                    onComplete: function(edit_form) {						
						document.getElementById('state').innerHTML = edit_form.responseText;
                    }
                });
            }
        </script>");
		
       if(Mage::registry('customeredit_data'))
		{
			$results = Mage::registry('customeredit_data');
			$result = $results->getData();
			
			$customerAddId = $result['default_billing'];
			
			$customAddress = Mage::getModel('customer/address')->load($customerAddId);
			
			$result['city'] = $customAddress['city'];
			$result['country_id'] = $customAddress['country_id'];
			$result['state'] = $customAddress['region'];
			$result['zipcode'] = $customAddress['postcode'];
			$result['telephone'] = $customAddress['telephone'];
			$result['street_address'] = $customAddress['street'];
			$result['fax'] = $customAddress['fax'];
			$result['vat_no'] = $customAddress['vat_id'];
			$result['company'] = $customAddress['company'];
			
			$form->setValues($result);
		}
		
		return parent::_prepareForm();
	}
		
}