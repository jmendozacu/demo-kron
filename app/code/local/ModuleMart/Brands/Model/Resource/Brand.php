<?php
 /**
 * ModuleMart_Brands extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Module-Mart License
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.modulemart.com/license.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to modules@modulemart.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * versions in the future. If you wish to customize this extension for your
 * needs please refer to http://www.modulemart.com for more information.
 *
 * @category   ModuleMart
 * @package    ModuleMart_Brands
 * @author-email  modules@modulemart.com
 * @copyright  Copyright 2014 © modulemart.com. All Rights Reserved
 */
class ModuleMart_Brands_Model_Resource_Brand extends Mage_Core_Model_Resource_Db_Abstract{
	/**
	 * constructor
	 * @access public
	 */
	public function _construct(){
		$this->_init('brands/brand', 'entity_id');
	}
	
	/**
	 * Get store ids to which specified item is assigned
	 * @access public
	 * @param int $brandId
	 */
	public function lookupStoreIds($brandId){
		$adapter = $this->_getReadAdapter();
		$select  = $adapter->select()
			->from($this->getTable('brands/brand_store'), 'store_id')
			->where('brand_id = ?',(int)$brandId);
		return $adapter->fetchCol($select);
	}
	/**
	 * Perform operations after object load
	 * @access public
	 * @param Mage_Core_Model_Abstract $object
	 */
	protected function _afterLoad(Mage_Core_Model_Abstract $object){
		if ($object->getId()) {
			$stores = $this->lookupStoreIds($object->getId());
			$object->setData('store_id', $stores);
		}
		return parent::_afterLoad($object);
	}

	/**
	 * Retrieve select object for load object data
	 *
	 * @param string $field
	 * @param mixed $value
	 * @param ModuleMart_Brands_Model_Brand $object
	 */
	protected function _getLoadSelect($field, $value, $object){
		$select = parent::_getLoadSelect($field, $value, $object);
		if ($object->getStoreId()) {
			$storeIds = array(Mage_Core_Model_App::ADMIN_STORE_ID, (int)$object->getStoreId());
			$select->join(
				array('brands_brand_store' => $this->getTable('brands/brand_store')),
				$this->getMainTable() . '.entity_id = brands_brand_store.brand_id',
				array()
			)
			->where('brands_brand_store.store_id IN (?)', $storeIds)
			->order('brands_brand_store.store_id DESC')
			->limit(1);
		}
		return $select;
	}
	/**
	 * Assign brand to store views
	 * @access protected
	 * @param Mage_Core_Model_Abstract $object
	 */
	protected function _afterSave(Mage_Core_Model_Abstract $object){
		$oldStores = $this->lookupStoreIds($object->getId());
		$newStores = (array)$object->getStores();
		if (empty($newStores)) {
			$newStores = (array)$object->getStoreId();
		}
		$table  = $this->getTable('brands/brand_store');
		$insert = array_diff($newStores, $oldStores);
		$delete = array_diff($oldStores, $newStores);
		if ($delete) {
			$where = array(
				'brand_id = ?' => (int) $object->getId(),
				'store_id IN (?)' => $delete
			);
			$this->_getWriteAdapter()->delete($table, $where);
		}
		if ($insert) {
			$data = array();
			foreach ($insert as $storeId) {
				$data[] = array(
					'brand_id'  => (int) $object->getId(),
					'store_id' => (int) $storeId
				);
			}
			$this->_getWriteAdapter()->insertMultiple($table, $data);
		}
		return parent::_afterSave($object);
	}	/**
	 * check url key
	 * @access public
	 * @param string $urlKey
	 * @param bool $active
	 */
	public function checkUrlKey($urlKey, $storeId, $active = true){
		$stores = array(Mage_Core_Model_App::ADMIN_STORE_ID, $storeId);
		$select = $this->_initCheckUrlKeySelect($urlKey, $stores);
		if (!is_null($active)) {
			$select->where('e.status = ?', $active);
		}
		$select->reset(Zend_Db_Select::COLUMNS)
			->columns('e.entity_id')
			->limit(1);
		
		return $this->_getReadAdapter()->fetchOne($select);
	}
	/**
	 * init the check select
	 * @access protected
	 * @param string $urlKey
 	 * @param array $store
	 */
	protected function _initCheckUrlKeySelect($urlKey, $store){
		$select = $this->_getReadAdapter()->select()
			->from(array('e' => $this->getMainTable()))
			->join(
				array('es' => $this->getTable('brands/brand_store')),
				'e.entity_id = es.brand_id',
				array())
			->where('e.url_key = ?', $urlKey)
			->where('es.store_id IN (?)', $store);
		return $select;
	}
	/**
	 * Check for unique URL key
	 * @access public
	 * @param Mage_Core_Model_Abstract $object
	 */
	public function getIsUniqueUrlKey(Mage_Core_Model_Abstract $object){
		if (Mage::app()->isSingleStoreMode() || !$object->hasStores()) {
			$stores = array(Mage_Core_Model_App::ADMIN_STORE_ID);
		} 
		else {
			$stores = (array)$object->getData('stores');
		}
		$select = $this->_initCheckUrlKeySelect($object->getData('url_key'), $stores);
		if ($object->getId()) {
			$select->where('e.entity_id <> ?', $object->getId());
		}
		if ($this->_getWriteAdapter()->fetchRow($select)) {
			return false;
		}
		return true;
	}
	/**
	 * Check if the URL key is numeric
	 * @access public
	 * @param Mage_Core_Model_Abstract $object
	 */
	protected function isNumericUrlKey(Mage_Core_Model_Abstract $object){
		return preg_match('/^[0-9]+$/', $object->getData('url_key'));
	}
	/**
	 * Checkif the URL key is valid
	 * @access public
	 * @param Mage_Core_Model_Abstract $object
	 */
	protected function isValidUrlKey(Mage_Core_Model_Abstract $object){
		return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('url_key'));
	}
	/**
	 * validate before saving
	 * @access protected
	 * @param $object
	 */
	protected function _beforeSave(Mage_Core_Model_Abstract $object){
		if (!$this->getIsUniqueUrlKey($object)) {
			Mage::throwException(Mage::helper('brands')->__('URL key already exists.'));
		}
		if (!$this->isValidUrlKey($object)) {
			Mage::throwException(Mage::helper('brands')->__('The URL key contains capital letters or disallowed symbols.'));
		}
		if ($this->isNumericUrlKey($object)) {
			Mage::throwException(Mage::helper('brands')->__('The URL key cannot consist only of numbers.'));
		}
		return parent::_beforeSave($object);
	}}