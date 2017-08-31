<?php 
class Kronosav_Loan_Block_Adminhtml_Customer_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		
		$form = new Varien_Data_Form(array(
		 'id' => 'edit_form',
		'action' => $this->getUrl('*/*/save', array('id' =>$this->getRequest()->getParam('id'))),
		'method' => 'post',
		'enctype' => 'multipart/form-data'));
		$this->setForm($form);
		$fieldset = $form->addFieldset('loan_form', array('legend'=>Mage::helper('loan')->__('Loan Information')));
			
		
		if(Mage::registry('customeredit_data')->getData())
		{  	
			$fieldset->addField('entity_id', 'hidden', array(
			'label'     => Mage::helper('loan')->__('Entity ID'),
			//'class'     => 'required-entry',
			//'required'  => true,
			//'readonly' => true,
			'name'      => 'entity_id',
			'value'	=>$entityid,
		));
		
		}
		$fieldset->addField('status', 'select', array(
			'label'     => Mage::helper('loan')->__('Status'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'status',
			'values' => array(
			  '1'=>'New'),
		));			
        $fieldset->addField('prefix', 'text', array(
			'label'     => Mage::helper('loan')->__('Prefix'),
			'name'      => 'prefix',
			'class'     => 'validate-alpha',
		));		
	   $fieldset->addField('firstname', 'text', array(
			'label'     => Mage::helper('loan')->__('First Name'),
			'class'     => 'required-entry validate-alpha',
			'required'  => true,
			'name'      => 'firstname',
		));
		$fieldset->addField('middlename','text', array(
			'label'     => Mage::helper('loan')->__('Middle Name'),
			'name'      => 'lastname ',
			'class'     => 'validate-alpha',
		));
		$fieldset->addField('lastname','text', array(
			'label'     => Mage::helper('loan')->__('Last Name'),
			'class'     => 'required-entry validate-alpha',
			'required'  => true,
			'name'      => 'lastname',
		));
		$fieldset->addField('suffix', 'text', array(
			'label'     => Mage::helper('loan')->__('Prefix'),
			'name'      => 'suffix',
			'class'     => 'validate-alpha',
		));
		$fieldset->addField('email', 'text', array(
			'label'     => Mage::helper('loan')->__('Email'),
			'class'     => 'required-entry validate-email',
			'required'  => true,
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
		$fieldset->addField('loan_description', 'textarea', array(
			'label'     => Mage::helper('loan')->__('Loan Description'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'loan_description',
		));	
		
		$depent1 = $fieldset->addField('product_name', 'note', array(
			'label'     => Mage::helper('loan')->__('Product name'),
			'text'     => implode("<br/>",$this->getproductvalue('name')),
			'name'      => 'product_name',
		));	
		$fieldset->addField('deposit_amount', 'text', array(
			'label'     => Mage::helper('loan')->__('Deposit Amount'),
			'class'     => 'required-entry validate-number',
			'required'  => true,
			'name'      => 'deposit_amount',
			
		));	
		$fieldset->addField('loan_from_date', 'date',array(
          'name'      =>    'loan_from_date', /* should match with your table column name where the data should be inserted */
          'time'      =>    true,
          'class'     => 'required-entry validate-date',
          'required'  => true,        
          'format'    => 'yyyy-MM-dd',
          'label'     => Mage::helper('loan')->__('Loan From Date'),
          'image'     => $this->getSkinUrl('images/grid-cal.gif')
       ));
		
		$fieldset->addField('loan_to_date', 'date',array(
          'name'      =>    'loan_to_date', /* should match with your table column name where the data should be inserted */
          'time'      =>    true,
          'class'     => 'required-entry validate-date',
          'required'  => true,        
          'format'    => 'yyyy-MM-dd',
          'label'     => Mage::helper('loan')->__('Loan To Date'),
          'image'     => $this->getSkinUrl('images/grid-cal.gif')
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
		
	    if(Mage::registry('customeredit_data'))
		{
			$results = Mage::registry('customeredit_data');
			
			$result = $results->getData();
			$customerAddId = $result['default_billing'];
			
			$customAddress = Mage::getModel('customer/address')->load($customerAddId);
			
			$result['city'] = $customAddress['city'];
			$result['country_id'] = $customAddress['country_id'];
			$result['state'] = $state;
			$result['zip_code'] = $customAddress['postcode'];
			$result['telephone'] = $customAddress['telephone'];
			$result['street_address'] = $customAddress['street'];
			$result['fax'] = $customAddress['fax'];
			$form->setValues($result);
			//Mage::unregister('customeredit_data');
		}
		return parent::_prepareForm();
	}
	
	
	public function getproductvalue($value)
	{
		$Values = array();
		 $selectedProducts = Mage::getSingleton('adminhtml/session')->getProductDetails();
		foreach($selectedProducts as $product)
		 {
			$Values[] = $product[$value];
		 }
		return $Values;
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