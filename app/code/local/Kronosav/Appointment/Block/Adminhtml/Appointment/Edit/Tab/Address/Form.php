<?php 
class Kronosav_Appointment_Block_Adminhtml_Appointment_Edit_Tab_Address_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form(array(
             'id' => 'edit_form',
            'action' => $this->getUrl('*/*/customersave', array('id' =>$this->getRequest()->getParam('id'))),
            'method' => 'post',
            'enctype' => 'multipart/form-data'));
			
		$this->setForm($form);
		
		$fieldset = $form->addFieldset('appointment_form', array('legend'=>Mage::helper('appointment')->__('Appointment Information')));
		
		 $fieldset->addField('appointment_id', 'hidden', array(
			'label'     => Mage::helper('appointment')->__('Appointment ID'),
			'name'      => 'appointment_id',
			'required'  => true,
			'readonly' => true,
			
		));
		
		$fieldset->addField('company', 'text', array(
			'label'     => Mage::helper('appointment')->__('Company'),
			'name'      => 'company',
		));			
		
		$fieldset->addField('street_address', 'text', array(
			'label'     => Mage::helper('appointment')->__('Street Address'),
			'name'      => 'street_address',
		));	
		
		$fieldset->addField('city', 'text', array(
			'label'     => Mage::helper('appointment')->__('City'),
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
		
	
        $fieldset->addField('zip_code', 'text', array(
			'label'     => Mage::helper('appointment')->__('Zip Code'),
			'name'      => 'zip_code',
		));
		
		$fieldset->addField('telephone', 'text', array(
			'label'     => Mage::helper('appointment')->__('Telephone'),
			'name'      => 'telephone',
		));
		
		$fieldset->addField('fax', 'text', array(
			'label'     => Mage::helper('appointment')->__('Fax'),
			'name'      => 'fax',
		));
		
		$fieldset->addField('vat_no', 'text', array(
			'label'     => Mage::helper('appointment')->__('VAT Number'),
			'name'      => 'vat_no',
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
		
		if ( Mage::registry('appointmentedit_data') ) 
		{
			$result = Mage::registry('appointmentedit_data')->getData();
			$results = Mage::registry('customeredit_data')->getData();
			
			$customerAddId = $results['default_billing'];
			
			$customAddress = Mage::getModel('customer/address')->load($customerAddId);
			
			$firstname=$results["firstname"];
			$lastname=$results["lastname"];
			$email=$results["email"];
			
			$result["firstname"] = $firstname;
			$result["lastname"] = $lastname;
			$result["email"] = $email;
			
			$result['city'] = $customAddress['city'];
			$result['country_id'] = $customAddress['country_id'];
			$result['state'] = $customAddress['region'];
			$result['zip_code'] = $customAddress['postcode'];
			$result['telephone'] = $customAddress['telephone'];
			$result['street_address'] = $customAddress['street'];
			$result['fax'] = $customAddress['fax'];
			$result['company'] = $customAddress['company'];
			$result['vat_no'] = $customAddress['vat_id'];
			
			$result["address2"] = $this->getValue2($this->getRequest()->getParam('id'));
			$form->setValues($result);
		
		}
		elseif(Mage::registry('customeredit_data'))
		{
			$results = Mage::registry('customeredit_data')->getData();
			$form->setValues($results);
		}
		
		return parent::_prepareForm();
	}
}