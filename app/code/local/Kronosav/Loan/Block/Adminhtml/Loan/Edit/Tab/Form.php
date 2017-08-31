<?php 
class Kronosav_Loan_Block_Adminhtml_Loan_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form(array(
		 'id' => 'edit_form',
		'action' => $this->getUrl('*/*/customersave', array('id' =>$this->getRequest()->getParam('id'))),
		'method' => 'post',
		'enctype' => 'multipart/form-data'));
		
		$this->setForm($form);
		$fieldset = $form->addFieldset('loan_form', array('legend'=>Mage::helper('loan')->__('Loan Information')));
		
			
		$fieldset->addField('loan_id', 'hidden', array(
			'label'     => Mage::helper('loan')->__('Loan ID'),
			'name'      => 'loan_id',
			'required'  => true,
			'readonly' => true,
		));
		$fieldset->addField('status', 'select', array(
			'label'     => Mage::helper('loan')->__('Status'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'status',
			'values' => array('1'=>'New',
			  '2'=>'Completed',
			  '3'=>'Cancelled')
		));	
	    $fieldset->addField('firstname', 'text', array(
			'label'     => Mage::helper('loan')->__('First Name'),
			'class'     => 'required-entry',
			'required'  => true,
			'readonly' => true,
			'name'      => 'firstname',
		));
		
		$fieldset->addField('lastname', 'text', array(
			'label'     => Mage::helper('loan')->__('Last Name'),
			'class'     => 'required-entry',
			'required'  => true,
			'readonly' => true,
			'name'      => 'lastname',
		));
		
		$fieldset->addField('email', 'text', array(
			'label'     => Mage::helper('loan')->__('Email'),
			'class'     => 'required-entry',
			'required'  => true,
			'readonly' => true,
			'name'      => 'email',
		));	
		$fieldset->addField('street_address', 'text', array(
			'label'     => Mage::helper('loan')->__('Street Address'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'street_address',
		));	
		$fieldset->addField('city', 'text', array(
			'label'     => Mage::helper('loan')->__('City'),
			'class'     => 'required-entry validate-alpha',
			'required'  => true,
			'name'      => 'city',
		));
		 $fieldset->addField('zip_code', 'text', array(
			'label'     => Mage::helper('loan')->__('Zip Code'),
			'class'     => 'required-entry',
			'name'      => 'zip_code',
			'required'  => true,
		));
		$fieldset->addField('telephone', 'text', array(
			'label'     => Mage::helper('loan')->__('Telephone'),
			'class'     => 'required-entry validate-number',
			'required'  => true,
			'name'      => 'telephone',
			
		));
		$fieldset->addField('fax', 'text', array(
			'label'     => Mage::helper('loan')->__('Fax'),
			'name'      => 'fax',
			'class'     => 'validate-fax',
		));
		 $country = $fieldset->addField('country_id', 'select', array(
        'name'  => 'country_id',
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
					'class'     => 'validate-alpha',
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
					'class'     => 'validate-alpha',
				));
		}
		$fieldset->addField('loan_description', 'textarea', array(
			'label'     => Mage::helper('loan')->__('loan Description'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'loan_description',
		));	
		$fieldset->addField('product_name', 'note', array(
			'label'     => Mage::helper('loan')->__('Product name'),
			'text'     => implode("<br/>",$this->getproductvalue()),
			'name'      => 'product_name',
						
		));	
		$fieldset->addField('deposit_amount', 'text', array(
			'label'     => Mage::helper('loan')->__('Deposit Amount'),
			'class'     => 'required-entry validate-number',
			'required'  => true,
			'name'      => 'deposit_amount',
		));	
		
		$fieldset->addField('loan_from_date', 'date',array(
          'name'      =>    'loan_from_date', 
          'class'     => 'required-entry validate-date',
          'required'  => true,        
          'format'    => 'yyyy-MM-dd',
          'label'     => Mage::helper('loan')->__('Loan From Date'),
          'image'     => $this->getSkinUrl('images/grid-cal.gif')
        ));
		
		$fieldset->addField('loan_to_date', 'date',array(
          'name'      =>    'loan_to_date', /* should match with your table column name where the data should be inserted */
          'class'     => 'required-entry validate-date',
          'required'  => true,        
          'format'    => 'yyyy-MM-dd',
          'label'     => Mage::helper('loan')->__('Loan To Date'),
          'image'     => $this->getSkinUrl('images/grid-cal.gif')
        ));
		
	    // $fieldset->addField('loan_to_time', 'time', array(
          // 'label'     => Mage::helper('loan')->__('loan To Time'),
          // 'class'     => 'required-entry',
          // 'required'  => true,
          // 'name'      => 'loan_to_time',
          // 'value'  => '12,04,15',
          // //'disabled' => false,
          // //'readonly' => false,
          // //'tabindex' => 1
        // ));
			
		 $country->setAfterElementHtml("
		<script type=\"text/javascript\">
			

		function getstate(selectElement){
			
			var reloadurl = '". $this->getUrl('loan/adminhtml_loan/state') . "country/' + selectElement.value;
			
			 new Ajax.Request(reloadurl, {
                    method: 'get',
                    onLoading: function (edit_form) {
                        //document.getElementById('state').innerHTML = 'Loading..';
						jQuery('#state').parent().addClass('state_chagnes');
						jQuery('#state').html('Loading...');
                    },
                    onComplete: function(edit_form) {						
						//document.getElementById('state').innerHTML = edit_form.responseText;
						jQuery('.state_chagnes').html(edit_form.responseText);
                    }
                });
		}    
        </script>");	
		
		if ( Mage::registry('loanedit_data') ) 
		{
			$result = Mage::registry('loanedit_data')->getData();
			
			$results = Mage::registry('customeredit_data')->getData();
			$firstname=$results["firstname"];
			$lastname=$results["lastname"];
			$email=$results["email"];
			$customerAddId = $results['default_billing'];
			
			$customAddress = Mage::getModel('customer/address')->load($customerAddId);
			
			$result['city'] = $customAddress['city'];
			$result['country_id'] = $customAddress['country_id'];
			
			$state = $customAddress['region'];
			
			$result['state'] = $state;
			
			$result['zip_code'] = $customAddress['postcode'];
			$result['telephone'] = $customAddress['telephone'];
			$result['street_address'] = $customAddress['street'];
			$result['fax'] = $customAddress['fax'];
			$result["firstname"] = $firstname;
			$result["lastname"] = $lastname;
			$result["email"] = $email;
			
			$form->setValues($result);
		}
		elseif(Mage::registry('customeredit_data')){
			$results = Mage::registry('customeredit_data')->getData();
			$form->setValues($results);
			
		}
		return parent::_prepareForm();
	}
	
	 public function getproductvalue()
	{
	     $result = Mage::registry('loanedit_data')->getData();
		 
		 $id=$result['loan_id'];
		
		 $loanProducts = Mage::getModel('loan/product')->getCollection()
								->addFieldToFilter("loan_id",$id);
				$selectedProducts = $loanProducts->getData(); 
				foreach($selectedProducts as $product)
				{
					$productId = $product['product_id']."<br>";
					$collection=Mage::getModel('catalog/product')->load($productId);
					$Names[]=$collection->getData('name');
				}
				//$productName = implode(",",$Names);
				return $Names;
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
