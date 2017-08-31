<?php 
class Kronosav_Loan_Block_Adminhtml_Product_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
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
			
		$customer_field_type = 'text';
		
		if(Mage::registry('customeredit_data'))
		{
		    $customer_field_type = 'label';
			$fieldset->addField('entity_id', 'hidden', array(
			'label'     => Mage::helper('loan')->__('Entity ID'),
			'class'     => 'required-entry',
			'required'  => true,
			//'readonly' => true,
			'name'      => 'entity_id',
			'value'	=>$entityid,
		));
		
		}		
	   $fieldset->addField('firstname', $customer_field_type, array(
			'label'     => Mage::helper('loan')->__('First Name'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'firstname',
		));
		
		$fieldset->addField('lastname', $customer_field_type, array(
			'label'     => Mage::helper('loan')->__('Last Name'),
			'class'     => 'required-entry',
			'required'  => true,
			
			'name'      => 'lastname',
		));
		$fieldset->addField('email', $customer_field_type, array(
			'label'     => Mage::helper('loan')->__('Email'),
			'class'     => 'required-entry',
			'required'  => true,
			
			'name'      => 'email',
		));	
		$fieldset->addField('loan_description', 'textarea', array(
			'label'     => Mage::helper('loan')->__('Loan Description'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'loan_description',
		));	
		$fieldset->addField('product_name', 'select', array(
			'label'     => Mage::helper('loan')->__('Product name'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'product_name',
			'values' => $this->addproductname(),
		));	
			$fieldset->addField('deposit_amount', 'text', array(
			'label'     => Mage::helper('loan')->__('Deposit Amount'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'deposit_amount',
			
		));	
		$fieldset->addField('loan_from_date', 'date',array(
          'name'      =>    'loan_from_time', /* should match with your table column name where the data should be inserted */
          'time'      =>    true,
          'class'     => 'required-entry',
          'required'  => true,        
           'format'    => 'yyyy-MM-dd HH:mm:ss',
          'label'     => Mage::helper('loan')->__('Loan From Date'),
          'image'     => $this->getSkinUrl('images/grid-cal.gif')
       ));
		
		$fieldset->addField('loan_to_date', 'date',array(
          'name'      =>    'loan_to_time', /* should match with your table column name where the data should be inserted */
          'time'      =>    true,
          'class'     => 'required-entry',
          'required'  => true,        
          'format'    => 'yyyy-MM-dd HH:mm:ss',
          'label'     => Mage::helper('loan')->__('Loan To Date'),
          'image'     => $this->getSkinUrl('images/grid-cal.gif')
       ));
	 $fieldset->addField('status', 'select', array(
			'label'     => Mage::helper('loan')->__('Status'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'status',
			'values' => array(
			  '1'=>'New',
			  '2'=>'Completed'),
		));	
		
		
       if(Mage::registry('customeredit_data'))
		{
			$results = Mage::registry('customeredit_data');
			$result = $results->getData();
			$form->setValues($result);
			//Mage::unregister('customeredit_data');
		}
		return parent::_prepareForm();
	}
	
	public function addproductname()
	{
	    $option = "";
		$collection=Mage::getModel('catalog/product')->getCollection()->getAllIds();
		$i = 0;	
	     foreach ($collection as $collections)
		  {
			   $product=Mage::getModel('catalog/product')->load($collections);
						
				$option[$i] = array('value'=>$product->getID(), 'label'=>Mage::helper('loan')->__($product->getName()));
				$i++;
		  }
		 
		 return $option;
	 }
	
}