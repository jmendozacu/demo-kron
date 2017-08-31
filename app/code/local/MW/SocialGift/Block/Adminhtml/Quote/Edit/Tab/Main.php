<?php
class MW_SocialGift_Block_Adminhtml_Quote_Edit_Tab_Main
extends Mage_Adminhtml_Block_Widget_Form
implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
	/**
	* Prepare content for tab
	*
	* @return string
	*/
	public function getTabLabel()
	{
		return Mage::helper('salesrule')->__('General');
	}

	/**
	* Prepare title for tab
	*
	* @return string
	*/
	public function getTabTitle()
	{
		return Mage::helper('salesrule')->__('General');
	}

	/**
	* Returns status flag about this tab can be showen or not
	*
	* @return TRUE
	*/
	public function canShowTab()
	{
		return TRUE;
	}

	/**
	* Returns status flag about this tab hidden or not
	*
	* @return TRUE
	*/
	public function isHidden()
	{
		return FALSE;
	}

	protected function _prepareForm()
	{
		$model = Mage::registry('current_socialgift_quote_rule');

		$form = new Varien_Data_Form();

		$form->setHtmlIdPrefix('rule_');

		$fieldset = $form->addFieldset('base_fieldset', array(
			'legend'=>Mage::helper('salesrule')->__('General Information')
			)
		);

		$fieldset->addField('auto_apply', 'hidden', array(
            'name' => 'auto_apply'
        ));

		if ($model->getId()) {
			$fieldset->addField('rule_id', 'hidden', array(
			'name' => 'rule_id'
			));
		}

		$fieldset->addField('product_ids', 'hidden', array(
			'name' => 'product_ids'
		));

		$fieldset->addField('name', 'text', array(
			'name' => 'name',
			'label' => Mage::helper('salesrule')->__('Rule Name'),
			'title' => Mage::helper('salesrule')->__('Rule Name'),
			'required' => TRUE
		));

		$fieldset->addField('description', 'textarea', array(
			'name' => 'description',
			'label' => Mage::helper('salesrule')->__('Description'),
			'title' => Mage::helper('salesrule')->__('Description'),
			'style' => 'width: 91%; height: 100px;'
		));

		$fieldset->addField('is_active', 'select', array(
			'label'     => Mage::helper('salesrule')->__('Status'),
			'title'     => Mage::helper('salesrule')->__('Status'),
			'name'      => 'is_active',
			'required' => TRUE,
			'options'    => array(
				'1' => Mage::helper('salesrule')->__('Active'),
				'0' => Mage::helper('salesrule')->__('Inactive'),
			)
		));

		if (!$model->getId()) {
			$model->setData('is_active', '1');
		}

		if (!Mage::app()->isSingleStoreMode()) {
			$fieldset->addField('sg_website_ids', 'multiselect', array(
				'name'      => 'sg_website_ids[]',
				'label'     => Mage::helper('catalogrule')->__('Websites'),
				'title'     => Mage::helper('catalogrule')->__('Websites'),
				'required'  => TRUE,
				'values'    => Mage::getSingleton('adminhtml/system_store')->getWebsiteValuesForForm()
			));
		}
		else {
			$websiteId = Mage::app()->getStore(TRUE)->getWebsiteId();
			$fieldset->addField('sg_website_ids', 'hidden', array(
				'name'      => 'sg_website_ids[]',
				'value'     => $websiteId
			));
			$model->setWebsiteIds($websiteId);
		}

		$fieldset->addField('sg_customer_group_ids', 'multiselect', array(
            'name'      => 'sg_customer_group_ids[]',
            'label'     => Mage::helper('catalogrule')->__('Customer Groups'),
            'title'     => Mage::helper('catalogrule')->__('Customer Groups'),
            'required'  => TRUE,
            'values'    => Mage::getResourceModel('customer/group_collection')->toOptionArray()
        ));

		$fieldset->addField('all_allow_countries', 'select', array(
			'label'     => Mage::helper('salesrule')->__('Gift to Applicable Countries'),
			'title'     => Mage::helper('salesrule')->__('Gift to Applicable Countries'),
			'name'      => 'all_allow_countries',
			'options'    => array(
				'1' => Mage::helper('salesrule')->__('All Allowed Countries'),
				'0' => Mage::helper('salesrule')->__('Specific Countries'),
			)
		));

		$fieldset->addField('sg_countries', 'multiselect', array(
	        'name'      => 'sg_countries[]',
		    'label'     => Mage::helper('salesrule')->__('Allowed Countries'),
		    'title'     => Mage::helper('salesrule')->__('Allowed Countries'),
	        'values'    => Mage::getModel('adminhtml/system_config_source_country')->toOptionArray()
	    ));

		$fieldset->addField('uses_limit', 'text', array(
			'name' 		=> 'uses_limit',
			'label' 	=> Mage::helper('salesrule')->__('Uses limit'),
            'required'  => TRUE,
		));

		$fieldset->addField('times_used', 'text', array(
			'name' 		=> 'times_used',
			'label' 	=> Mage::helper('salesrule')->__('Times Used'),
			'disabled'	=> 'disabled'
		));
		$dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
		$fieldset->addField('from_date', 'date', array(
			'name'   => 'from_date',
			'label'  => Mage::helper('salesrule')->__('From Date'),
			'title'  => Mage::helper('salesrule')->__('From Date'),
			'image'  => $this->getSkinUrl('images/grid-cal.gif'),
			'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
			'format'       => $dateFormatIso,
            'required'  => TRUE,
		));
		$fieldset->addField('to_date', 'date', array(
			'name'   => 'to_date',
			'label'  => Mage::helper('salesrule')->__('To Date'),
			'title'  => Mage::helper('salesrule')->__('To Date'),
			'image'  => $this->getSkinUrl('images/grid-cal.gif'),
			'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
			'format'       => $dateFormatIso
		));

		if(!$model->getId()){
			//set the default value for is_rss feed to yes for new promotion
			$model->setIsRss(1);
		}

		$form->setValues($model->getData());

		$this->setForm($form);

		Mage::dispatchEvent('socialgift_quote_edit_tab_main_prepare_form', array('form' => $form));

		return parent::_prepareForm();
	}
}
