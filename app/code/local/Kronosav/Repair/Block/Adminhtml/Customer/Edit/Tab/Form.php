<?php
class Kronosav_Repair_Block_Adminhtml_Customer_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
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
		
		$fieldset->addField('entity_id', 'hidden', array(
			'label'     => Mage::helper('repair')->__('entity ID'),
			'name'      => 'entity_id',
			'readonly' => true,
		));
		$fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('repair')->__('Status'),
            'name'      => 'status',
            'values'    => array(
                array(
                    'value'     => 0,
                    'label'     => Mage::helper('repair')->__('New'),
                ),
 
                array(
                    'value'     => 1,
                    'label'     => Mage::helper('repair')->__('In Process'),
                ),
				array(
                    'value'     => 2,
                    'label'     => Mage::helper('repair')->__('Pick Up'),
                ),
				array(
                    'value'     => 3,
                    'label'     => Mage::helper('repair')->__('Completed'),
                ),
				array(
                    'value'     => 4,
                    'label'     => Mage::helper('repair')->__('Cancelled'),
                ),
				
            ),
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
            'class'     => 'required-entry validate-email',
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
		$dateFormatIso = Mage::app()->getLocale() ->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
		$fieldset->addField('repair_submission_date', 'date', array( 
			'name' => 'repair_submission_date', 
			'title' => Mage::helper('repair')->__('Show banner to date'), 
			'label' => Mage::helper('repair')->__('Repair Submission Date'), 
			'image' => $this->getSkinUrl('images/grid-cal.gif'), 
			'input_format' => Varien_Date::DATE_INTERNAL_FORMAT, 
			'format' => $dateFormatIso, 
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
			$firstname=$result["firstname"];
			$lastname=$result["lastname"];
			$middlename=$result["middlename"];
			$email=$result["email"];
			$customerAddId = $result["default_billing"];
			
			$customAddress = Mage::getModel('customer/address')->load($customerAddId);
			// print_r($customAddress->getData());exit;
			
			$result["city"] = $customAddress["city"];
			$result["country"] = $customAddress["country_id"];
			
			$state = $customAddress['region'];
			
			$result['state'] = $state;
			
			$result["zip_code"] = $customAddress['postcode'];
			$result["telephone"] = $customAddress['telephone'];
			$result["street_address"] = $customAddress['street'];
			$result["fax"] = $customAddress['fax'];
			$result["first_name"] = $firstname;
			$result["last_name"] = $lastname;
			$result["middle_name"] = $middlename;
			$result["email"] = $email;
			$form->setValues($result);
		}

		 /* if ( Mage::getSingleton('adminhtml/session')->getCustomereditData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getCustomereditData());
            Mage::getSingleton('adminhtml/session')->setCustomereditData(null);
        } elseif ( Mage::registry('customeredit_data') ) {
            $form->setValues(Mage::registry('customeredit_data')->getData());
        }  */
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
	
}