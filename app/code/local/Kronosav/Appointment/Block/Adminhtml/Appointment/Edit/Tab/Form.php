<?php 
class Kronosav_Appointment_Block_Adminhtml_Appointment_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form(array(
             'id' => 'edit_form',
            'action' => $this->getUrl('*/*/customersave', array('id' =>$this->getRequest()->getParam('id'))),
            'method' => 'post',
            'enctype' => 'multipart/form-data'));
			
		$getDatas = get_class_methods(Varien_Data_Form);
		
		$this->setForm($form);
		
		$fieldset = $form->addFieldset('appointment_form', array('legend'=>Mage::helper('appointment')->__('Appointment Information')));
		
		 $fieldset->addField('appointment_id', 'hidden', array(
			'label'     => Mage::helper('appointment')->__('Appointment ID'),
			'name'      => 'appointment_id',
			'required'  => true,
		));
		
		$fieldset->addField('status', 'select', array(
			'label'     => Mage::helper('appointment')->__('Status'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'status',
			'values' => $this->statusoption(),
		));
		
		$fieldset->addField('prefix', 'text', array(
			'label'     => Mage::helper('appointment')->__('Prefix'),
			'name'      => 'prefix',
			'class'     => 'validate-alpha',
		));	
		
	   $fieldset->addField('firstname', 'text', array(
			'label'     => Mage::helper('appointment')->__('First Name'),
			'class'     => 'required-entry validate-alpha',
			'name'      => 'firstname',
			'required'  => true,
		));
		
		$fieldset->addField('middlename','text', array(
			'label'     => Mage::helper('appointment')->__('Middle Name'),
			'name'      => 'middle',
			'class'     => 'validate-alpha',
		));
		
		$fieldset->addField('lastname', 'text', array(
			'label'     => Mage::helper('appointment')->__('Last Name'),
			'class'     => 'required-entry validate-alpha',
			'required'  => true,
			'name'      => 'lastname',
		));
		
		$fieldset->addField('suffix', 'text', array(
			'label'     => Mage::helper('appointment')->__('Suffix'),
			'name'      => 'suffix',
			'class'     => 'validate-alpha',
		));
		
		$fieldset->addField('email', 'text', array(
			'label'     => Mage::helper('appointment')->__('Email'),
			'class'     => 'required-entry validate-email',
			'required'  => true,
			'name'      => 'email',
		));
		
		$fieldset->addField('appointment_description', 'textarea', array(
			'label'     => Mage::helper('appointment')->__('Appointment Description'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'appointment_description',
		));	
			
		$fieldset->addField('appointment_date', 'date', array(
			'name'               => 'appointment_date',
			'label'              => Mage::helper('appointment')->__('Appointment Date'),
			'tabindex'           => 1,
			'class'              => 'required-entry validate-date',
			'required'           => true,
			'format'             => 'yyyy-MM-dd',
			'image'              => $this->getSkinUrl('images/grid-cal.gif'),
			// 'format'             => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT) ,
			// 'value'              => date( Mage::app()->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
                                  // strtotime('next weekday') )
		));
				
		$fieldset->addField('appointment_time', 'select',array(
          'name'      =>    'appointment_time', 
          'time'      =>    true,
          'class'     => 'required-entry',
          'required'  => true,        
          'label'     => Mage::helper('appointment')->__('Appointment To Time'),
		  'values' => $this->getappointmenttime(),
        ));
		
		
		

		$fieldset->addField('location', 'select', array(
			'label'     => Mage::helper('appointment')->__('Location'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'location',
			'values' => array(
			  'In Store'=>'In Store',
			  'Customer Premises'=>'Customer Premises'),
		));
		
		$fieldset->addField('address1', 'note', array(
			'text'     => $this->getValue1(),
			'name'      => 'address1',
		));	
		
		$fieldset->addField('app_telephone', 'text', array(
			'label'     => Mage::helper('appointment')->__('Telephone'),
			'class'     => 'required-entry validate-number',
			'name'      => 'app_telephone',
			'required'  => true,
		));
		
		$fieldset->addField('street_address', 'text', array(
			'label'     => Mage::helper('appointment')->__('Street Address'),
			'name'      => 'street_address',
			'required'  => true,
		));	
		
		$fieldset->addField('city', 'text', array(
			'label'     => Mage::helper('appointment')->__('City'),
			'name'      => 'city',
			'class'     => 'validate-alpha',
			'required'  => true,
		));
		
		$country = $fieldset->addField('country_id', 'select', array(
        'name'  => 'country_id',
        'label' => 'Country',
		'required'  => true,
        'values'    => Mage::getModel('adminhtml/system_config_source_country')->toOptionArray(),
		'onchange' => 'getstate(this)',
		));
	
		if(Mage::registry('appointmentedit_data')) 
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
					'label'      => Mage::helper('appointment')->__('State'),
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
	
        $fieldset->addField('zip_code', 'text', array(
			'label'     => Mage::helper('appointment')->__('Zip Code'),
			'name'      => 'zip_code',
			'class'     => 'required-entry',
			'required'  => true,
		));
		
		$fieldset->addField('telephone', 'text', array(
			'label'     => Mage::helper('appointment')->__('Telephone'),
			'name'      => 'telephone',
			'class'     => 'validate-number',
			'required'  => true,
		));
		
		$fieldset->addField('fax', 'text', array(
			'label'     => Mage::helper('appointment')->__('Fax'),
			'name'      => 'fax',
			'class'     => 'validate-fax'
			//'required'  => true,
		));
		
		$country->setAfterElementHtml("
		<script type=\"text/javascript\">
			
            function getstate(selectElement){
				
                var reloadurl = '". $this
                 ->getUrl('appointment/adminhtml_appointment/state') . "country/' + selectElement.value;
				
                new Ajax.Request(reloadurl, {
                    method: 'get',
                    onLoading: function (edit_form) {
						jQuery('#state').parent().addClass('state_chagnes');
						jQuery('#state').html('Loading...');
                        //parent.document.getElementById('state').innerHTML = 'Loading..';
                    },
                    onComplete: function(edit_form) {				
						//alert('done');
						jQuery('.state_chagnes').html(edit_form.responseText);
						//var t = parent.getElementsByTagName('state');
						//t.innerHTML = edit_form.responseText;
                    }
                });
            }
        </script>");	
		
		if ( Mage::registry('appointmentedit_data') ) 
		{
			$results = Mage::registry('appointmentedit_data')->getData();
			
			$result = Mage::registry('customeredit_data')->getData();
			
			$customerAddId = $result['default_billing'];
			
			$customAddress = Mage::getModel('customer/address')->load($customerAddId);
			$state = $customAddress['region'];
			$firstname=$result["firstname"];
			$lastname=$result["lastname"];
			$email=$result["email"];
			
			$result["firstname"] = $firstname;
			$result["lastname"] = $lastname;
			$result["email"] = $email;
			
			$result['city'] = $customAddress['city'];
			$result['country_id'] = $customAddress['country_id'];
			
			$getStates = Mage::getModel('directory/region')->loadByName($customAddress['region'], $customAddress['country_id']);
			
			$result['state'] = $state;
			
			$result['zip_code'] = $customAddress['postcode'];
			$result['telephone'] = $customAddress['telephone'];
			$result['street_address'] = $customAddress['street'];
			$result['fax'] = $customAddress['fax'];
			
			$appointment_id=$results['appointment_id'];
			$appointment_description=$results['appointment_description'];
			$result['appointment_date']=$results['appointment_date'];
			$result['appointment_time']=$results['appointment_time'];
			
			$result['location']=$results['location'];
			
			$result['appointment_id']=$appointment_id;
			$result['appointment_description']=$appointment_description;
			$result['app_telephone'] = $results['telephone'];
			
			$form->setValues($result);
		
		}
		elseif(Mage::registry('customeredit_data'))
		{
			$results = Mage::registry('customeredit_data')->getData();
			$form->setValues($results);
		}
		
		/**
		$this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($select->getHtmlId(), $select->getName())
            ->addFieldMap($depent1->getHtmlId(), $depent1->getName())
            ->addFieldMap($depent2->getHtmlId(), $depent2->getName())
            ->addFieldDependence(
                $depent1->getName(),
                $select->getName(),
                'In Store'
            )
            ->addFieldDependence(
                $depent2->getName(),
                $select->getName(),
                'Customer Premises'
            )
        );
		*/
		
		$this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap('location', 'location')
			->addFieldMap('address1', 'address1')
			->addFieldMap('app_telephone', 'app_telephone')
			->addFieldMap('street_address', 'street_address')
			->addFieldMap('city', 'city')
			->addFieldMap('country_id', 'country_id')
			->addFieldMap('state', 'state')
			->addFieldMap('zip_code', 'zip_code')
			->addFieldMap('telephone', 'telephone')
            ->addFieldMap('fax', 'fax')
            ->addFieldDependence(
                'address1',
                'location',
                'In Store')
			->addFieldDependence(
                'app_telephone',
                'location',
                'In Store')	
			->addFieldDependence('street_address',
                'location',
                'Customer Premises')
			->addFieldDependence('city',
                'location',
                'Customer Premises')
			->addFieldDependence('country_id',
                'location',
                'Customer Premises')
			->addFieldDependence('state',
                'location',
                'Customer Premises')
			->addFieldDependence('zip_code',
                'location',
                'Customer Premises')
			->addFieldDependence('telephone',
                'location',
                'Customer Premises')				
            ->addFieldDependence('fax',
                'location',
                'Customer Premises')	
        );
		
		return parent::_prepareForm();
	}
	
	public function getValue1()
	{
		$html = Mage::app()->getLayout()->createBlock('cms/block')->setBlockId('appointment_instore')->toHtml();
		
		return $html;
	}
	
	public function getValue2($id)
	{	
	    $getAppoinment = Mage::getModel('appointment/appointment')->load($id);
		
		if($getAppoinment->getAddress())
		{
			$html = $getAppoinment->getAddress();
		}
		else{
		
			$getDefaultBilling =  Mage::getModel('customer/customer')->load($getAppoinment->getEntityId())->getDefaultBilling();
			
			$billingAddress =  Mage::getModel('customer/address')->load($getDefaultBilling);
			
			if($billingAddress)
			{
				$getStreet = $billingAddress->getStreet();
				
				$html = $billingAddress->getFirstname();
				$html .= ',';
				$html .= $billingAddress->getLastname();
				$html .= ',';
				$html .= $getStreet[0];
				$html .= ',';
				$html .= $billingAddress->getCity();
				$html .= ',';
				$html .= $billingAddress->getRegion();
				$html .= ',';
				$html .= $billingAddress->getCountryId();
				$html .= ',';
				$html .= $billingAddress->getPostcode();
			}
			else
			{
				$html = '';
			}
		}
		
		return $html;
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
	 $options=Mage::registry('appointmentedit_data')->getData();
		$option=$options['status'];
		switch($option)
		{
		  case 1:
			   $optionslist=array(
					  '1'=>'New',
					  '2'=>'Completed',
					  '3'=>'Cancelled');
			   break;
	      case 2:
				$optionslist=array(
					  '2'=>'Completed');
				break;
		  case 3:
			    $optionslist=array(
				  '3'=>'Cancelled');
				break;
		}
		 return $optionslist;
	}
	
	public function getappointmenttime()
	{
		 $appointmenttime = array(
                        array( 'value' => '12.00AM', 'label' => '12.00AM' ),
                        array( 'value' => '12.15AM', 'label' => '12.15AM' ),
						array( 'value' => '12.30AM', 'label' => '12.30AM' ),
                        array( 'value' => '12.45AM', 'label' => '12.45AM' ),
						array( 'value' => '1.00AM', 'label' => '1.00AM' ),
                        array( 'value' => '1.15AM', 'label' => '1.15AM' ),
						array( 'value' => '1.30AM', 'label' => '1.30AM' ),
                        array( 'value' => '1.45AM', 'label' => '1.45AM' ),
						array( 'value' => '2.00AM', 'label' => '2.00AM' ),
                        array( 'value' => '2.15AM', 'label' => '2.15AM' ),
						array( 'value' => '2.30AM', 'label' => '2.30AM' ),
                        array( 'value' => '2.45AM', 'label' => '2.45AM' ),
						array( 'value' => '3.00AM', 'label' => '3.00AM' ),
                        array( 'value' => '3.15AM', 'label' => '3.15AM' ),
						array( 'value' => '3.30AM', 'label' => '3.30AM' ),
                        array( 'value' => '3.45AM', 'label' => '3.45AM' ),
						array( 'value' => '4.00AM', 'label' => '4.00AM' ),
                        array( 'value' => '4.15AM', 'label' => '4.15AM' ),
						array( 'value' => '4.30AM', 'label' => '4.30AM' ),
                        array( 'value' => '4.45AM', 'label' => '4.45AM' ),
						array( 'value' => '5.00AM', 'label' => '5.00AM' ),
                        array( 'value' => '5.15AM', 'label' => '5.15AM' ),
						array( 'value' => '5.30AM', 'label' => '5.30AM' ),
                        array( 'value' => '5.45AM', 'label' => '5.45AM' ),
						array( 'value' => '6.00AM', 'label' => '6.00AM' ),
                        array( 'value' => '6.15AM', 'label' => '6.15AM' ),
						array( 'value' => '6.30AM', 'label' => '6.30AM' ),
                        array( 'value' => '6.45AM', 'label' => '6.45AM' ),
						array( 'value' => '7.00AM', 'label' => '7.00AM' ),
                        array( 'value' => '7.15AM', 'label' => '7.15AM' ),
						array( 'value' => '7.30AM', 'label' => '7.30AM' ),
                        array( 'value' => '7.45AM', 'label' => '7.45AM' ),
						array( 'value' => '8.00AM', 'label' => '8.00AM' ),
                        array( 'value' => '8.15AM', 'label' => '8.15AM' ),
						array( 'value' => '8.30AM', 'label' => '8.30AM' ),
                        array( 'value' => '8.45AM', 'label' => '8.45AM' ),
						array( 'value' => '9.00AM', 'label' => '9.00AM' ),
                        array( 'value' => '9.15AM', 'label' => '9.15AM' ),
						array( 'value' => '9.30AM', 'label' => '9.30AM' ),
                        array( 'value' => '9.45AM', 'label' => '9.45AM' ),
						array( 'value' => '10.00AM', 'label' => '10.00AM' ),
                        array( 'value' => '10.15AM', 'label' => '10.15AM' ),
						array( 'value' => '10.30AM', 'label' => '10.30AM' ),
                        array( 'value' => '10.45AM', 'label' => '10.45AM' ),
						array( 'value' => '11.00AM', 'label' => '11.00AM' ),
                        array( 'value' => '11.15AM', 'label' => '11.15AM' ),
						array( 'value' => '11.30AM', 'label' => '11.30AM' ),
                        array( 'value' => '11.45AM', 'label' => '11.45AM' ),
						array( 'value' => '12.00PM', 'label' => '12.00PM' ),
                        array( 'value' => '12.15PM', 'label' => '12.15PM' ),
						array( 'value' => '12.30PM', 'label' => '12.30PM' ),
                        array( 'value' => '12.45PM', 'label' => '12.45PM' ),
						array( 'value' => '1.00PM', 'label' => '1.00PM' ),
                        array( 'value' => '1.15PM', 'label' => '1.15PM' ),
						array( 'value' => '1.30PM', 'label' => '1.30PM' ),
                        array( 'value' => '1.45PM', 'label' => '1.45PM' ),
						array( 'value' => '2.00PM', 'label' => '2.00PM' ),
                        array( 'value' => '2.15PM', 'label' => '2.15PM' ),
						array( 'value' => '2.30PM', 'label' => '2.30PM' ),
                        array( 'value' => '2.45PM', 'label' => '2.45PM' ),
						array( 'value' => '3.00PM', 'label' => '3.00PM' ),
                        array( 'value' => '3.15PM', 'label' => '3.15PM' ),
						array( 'value' => '3.30PM', 'label' => '3.30PM' ),
                        array( 'value' => '3.45PM', 'label' => '3.45PM' ),
						array( 'value' => '4.00PM', 'label' => '4.00PM' ),
                        array( 'value' => '4.15PM', 'label' => '4.15PM' ),
						array( 'value' => '4.30PM', 'label' => '4.30PM' ),
                        array( 'value' => '4.45PM', 'label' => '4.45PM' ),
						array( 'value' => '5.00PM', 'label' => '5.00PM' ),
                        array( 'value' => '5.15PM', 'label' => '5.15PM' ),
						array( 'value' => '5.30PM', 'label' => '5.30PM' ),
                        array( 'value' => '5.45PM', 'label' => '5.45PM' ),
						array( 'value' => '6.00PM', 'label' => '6.00PM' ),
                        array( 'value' => '6.15PM', 'label' => '6.15PM' ),
						array( 'value' => '6.30PM' , 'label' => '6.30PM' ),
                        array( 'value' => '6.45PM', 'label' => '6.45PM' ),
						array( 'value' => '7.00PM', 'label' => '7.00PM' ),
                        array( 'value' => '7.15PM', 'label' => '7.15PM' ),
						array( 'value' => '7.30PM', 'label' => '7.30PM' ),
                        array( 'value' => '7.45PM', 'label' => '7.45PM' ),
						array( 'value' => '8.00PM', 'label' => '8.00PM' ),
                        array( 'value' => '8.15PM', 'label' => '8.15PM' ),
						array( 'value' => '8.30PM', 'label' => '8.30PM' ),
                        array( 'value' => '8.45PM', 'label' => '8.45PM' ),
						array( 'value' => '9.00PM', 'label' => '9.00PM' ),
                        array( 'value' => '9.15PM', 'label' => '9.15PM' ),
						array( 'value' => '9.30PM', 'label' => '9.30PM' ),
                        array( 'value' => '9.45PM', 'label' => '9.45PM' ),
						array( 'value' => '10.00PM', 'label' => '10.00PM' ),
                        array( 'value' => '10.15PM', 'label' => '10.15PM' ),
						array( 'value' => '10.30PM', 'label' => '10.30PM' ),
                        array( 'value' => '10.45PM', 'label' => '10.45PM' ),
						array( 'value' => '11.00PM', 'label' => '11.00PM' ),
                        array( 'value' => '11.15PM', 'label' => '11.15PM' ),
						array( 'value' => '11.30PM', 'label' => '11.30PM' ),
                        array( 'value' => '11.45PM', 'label' => '11.45PM' )
            );
			
		return $appointmenttime;
	}
}