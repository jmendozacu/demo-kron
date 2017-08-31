<?php
class Kronosav_Repair_Block_Adminhtml_Repair_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('repair_form', array('legend'=>Mage::helper('repair')->__('Repair Log information')));
		
		$fieldset->addField('repair_id', 'hidden', array(
			'label'     => Mage::helper('repair')->__('Repair ID'),
			'name'      => 'repair_id',
			'readonly' => true,
		));
		$fieldset->addField('repair_submission_date', 'hidden', array(
			'label'     => Mage::helper('repair')->__('repair_submission_date'),
			'name'      => 'repair_submission_date',
			'readonly' => true,
		));
		$fieldset->addField('entity_id', 'hidden', array(
			'label'     => Mage::helper('repair')->__('entity ID'),
			'name'      => 'entity_id',
			'readonly' => true,
		));
		$fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('repair')->__('Status'),
            'name'      => 'status',
            'values'    => $this->statusoption(),
        ));
		
		$fieldset->addField('first_name', 'text', array(
            'label'     => Mage::helper('repair')->__('First Name'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'first_name',
        ));
		
		$fieldset->addField('middle_name', 'text', array(
            'label'     => Mage::helper('repair')->__('Middle Name'),
            'name'      => 'middle_name',
        ));
		
		$fieldset->addField('last_name', 'text', array(
            'label'     => Mage::helper('repair')->__('Last Name'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'last_name',
        ));
		$fieldset->addField('email', 'text', array(
            'label'     => Mage::helper('repair')->__('Email'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'email',
        ));
		$fieldset->addField('street_address', 'text', array(
            'label'     => Mage::helper('repair')->__('Street address'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'street_address',
        ));
		$fieldset->addField('city', 'text', array(
            'label'     => Mage::helper('repair')->__('City'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'city',
        ));
		$country = $fieldset->addField('country', 'select', array(
        'name'  => 'country',
        'label' => 'Country',
		'required'  => true,
        'values'    => Mage::getModel('adminhtml/system_config_source_country')->toOptionArray(),
		'onchange' => 'getstate(this)',
		));	
		
		if(Mage::registry('customeredit_data')) 
		{
			$customerResult = Mage::registry('customeredit_data')->getData();
			$customerAddId = $customerResult['default_billing'];
			$customAddress = Mage::getModel('customer/address')->load($customerAddId);			
						
			$countryName = Mage::getModel('directory/region')->getResourceCollection()->addCountryFilter($customAddress['country_id'])->load();
	
			$getCountryName = $countryName->getData();
			
			if(empty($getCountryName))
			{	
				$state = $customAddress['region'];
				
				$fieldset->addField('state', 'text', array(
					'name'      => 'state',
					'label'      => Mage::helper('loan')->__('State'),
					'required'  => true,
				));
			}
			else
			{
				$getStates = Mage::getModel('directory/region')->loadByName($customAddress['region'], $customAddress['country_id']);
				
				$state = $getStates['code'];
				
				$fieldset->addField('state', 'select', array(
				'name'  => 'state',
				'label' => 'State',
				'required'  => true,
				'values'    => $this->getStates($customAddress['country_id']),
				));
			}
		}
		else{
		
			$fieldset->addField('state', 'text', array(
					'name'      => 'state',
					'label'      => Mage::helper('loan')->__('State'),
					'required'  => true,
				));
		}
		
		/* $fieldset->addField('country', 'text', array(
            'label'     => Mage::helper('repair')->__('Country'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'country',
        )); */
		$fieldset->addField('zip_code', 'text', array(
            'label'     => Mage::helper('repair')->__('Zip code'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'zip_code',
        ));
		$fieldset->addField('telephone', 'text', array(
            'label'     => Mage::helper('repair')->__('Telephone'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'telephone',
        ));
		$fieldset->addField('fax', 'text', array(
            'label'     => Mage::helper('repair')->__('Fax'),
            'name'      => 'fax',
        ));
		$fieldset->addField('deposit_amount', 'text', array(
            'label'     => Mage::helper('repair')->__('Deposit Amount'),
            'name'      => 'deposit_amount',
        ));
		$fieldset->addField('product_make', 'text', array(
            'label'     => Mage::helper('repair')->__('Product Make'),
            'name'      => 'product_make',
        ));
		$fieldset->addField('product_model', 'text', array(
            'label'     => Mage::helper('repair')->__('Product Model'),
            'name'      => 'product_model',
        ));
		$fieldset->addField('serial_no', 'text', array(
            'label'     => Mage::helper('repair')->__('Serial Number'),
            'name'      => 'serial_no',
        ));
		$fieldset->addField('repair_details', 'text', array(
            'label'     => Mage::helper('repair')->__('Repair Details'),
            'name'      => 'repair_details',
        ));
		$fieldset->addField('miscellaneous_details', 'text', array(
            'label'     => Mage::helper('repair')->__('Miscellaneous Details'),
            'name'      => 'miscellaneous_details',
        ));
		
		
		$fieldset = $form->addFieldset('repair_form1');
		
		$fieldset->addField('repair_cost', 'text', array(
            'label'     => Mage::helper('repair')->__('Repair cost'),
            'name'      => 'repair_cost',
        ));
		$fieldset->addField('repair_time', 'text', array(
            'label'     => Mage::helper('repair')->__('Repair time(hours)'),
            'name'      => 'repair_time',
        ));
		$fieldset->addField('diagnostic_description', 'text', array(
            'label'     => Mage::helper('repair')->__('Diagnostic Description'),
            'name'      => 'diagnostic_description',
        ));
		$country->setAfterElementHtml("
		<script type=\"text/javascript\">
			
		function getstate(selectElement){
			
			var reloadurl = '". $this->getUrl('loan/adminhtml_loan/state') . "country/' + selectElement.value;
			
			new Ajax.Request(reloadurl, {
				method: 'get',
				onLoading: function (edit_form) {
					jQuery('#state').parent().addClass('state_chagnes');
					jQuery('#state').html('Loading...');
				},
				onComplete: function(edit_form) {				
					jQuery('.state_chagnes').html(edit_form.responseText);
				}
			});
		}
        </script>");		
		
		if ( Mage::registry('customeredit_data') ) 
		{
			$result = Mage::registry('customeredit_data')->getData();
			$results = Mage::registry('repairedit_data')->getData();
			$firstname=$result["firstname"];
			$lastname=$result["lastname"];
			$email=$result["email"];
			$customerAddId = $result['default_billing'];
			$results["first_name"] = $firstname;
			$results["last_name"] = $lastname;
			$results["middle_name"] = $middlename;
			$results["email"] = $email;			
			$results["product_make"] = $results["product_make"];
			$results["product_model"] = $results["product_model"];
			$results["serial_no"] = $results["serial_no"];
			$results["received_date"] = $results["received_date"];
			$results["updated_date"] = $results["updated_date"];
			$results["status"] = $results["status"];
			$results["deposit_amount"] = $results["deposit_amount"];
			$results["repair_cost"] = $results["repair_cost"];
			$results["repair_time"] = $results["repair_time"];
			$results["repair_details"] = $results["repair_details"];
			$results["diagnostic_description"] = $results["diagnostic_desc"];
			$results["repair_submission_date"] = $results["repair_submission_date"];
			$results["repair_delieverd_date"] = $results["repair_delieverd_date"];
			$results["miscellaneous_details"] = $results["miscellaneous_details"];
			
			$customAddress = Mage::getModel('customer/address')->load($customerAddId);
			$results["city"] = $customAddress["city"];
			$results["country"] = $customAddress["country_id"];
			
			$state = $customAddress['region'];
			
			$results["state"] = $state;
			
			$results["zip_code"] = $customAddress['postcode'];
			$results["telephone"] = $customAddress['telephone'];
			$results["street_address"] = $customAddress['street'];
			$results["fax"] = $customAddress['fax'];
			$form->setValues($results);
			
		}
		return parent::_prepareForm();
		
		
	}
	public function getStates($countryId)
	{
		$statearray = Mage::getModel('directory/region')->getResourceCollection()->addCountryFilter($countryId)->load();
	
		$getDataNew = $statearray->getData();
		
		$states = array();
		
		foreach($getDataNew as $state) 
		{
			$states[] = array( 'value' =>  $state['code'], 'label' => $state['default_name']);
		}
		
		return $states;
	}
	
	public function statusoption()
    {
        $options=Mage::registry('repairedit_data')->getData();
        $option=$options['status'];
        switch($option)
        {
          case 0:
               $optionslist=array(
                      '0'=>'New',
                      '1'=>'In Process',
                      '2'=>'Pick up',
                      '3'=>'Completed',
                      '4'=>'Cancelled');
               break;
          case 1:
                $optionslist=array(
                      '1'=>'In Process',
                      '2'=>'Pick up',
                      '3'=>'Completed',
                      '4'=>'Cancelled');
                break;
          case 2:
                $optionslist=array(
                  '2'=>'Pick Up',
				  '3'=>'Completed',
                  '4'=>'Cancelled');
                break;
		  case 3:
                $optionslist=array(
                  '3'=>'Completed');
                break;
		  case 4:
                $optionslist=array(
                  '4'=>'Cancelled');
                break;
		   		
        }
         return $optionslist;
    }
	
}