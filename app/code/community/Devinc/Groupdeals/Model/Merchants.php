<?php

class Devinc_Groupdeals_Model_Merchants extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('groupdeals/merchants');
    }
	
    public function isMerchant()
    {
        $user = Mage::getSingleton('admin/session');
		$userId = $user->getUser()->getUserId();
		$merchant = Mage::getModel('groupdeals/merchants')->load($userId, 'user_id');
		if ($merchant->getId()) {
			return $merchant;
		} else {
			return false;
		}
    }
	
    public function getPermission($type)
    {
    	if ($merchant = $this->isMerchant()) {
			$permissions = $merchant->getPermissions();
			$allow = Mage::getModel('license/module')->getDecodeString($permissions, $type);
			
			if ($allow==0) {
				return false;
			}
		} elseif ($type == 'approve') {
			return false;
		}
		
		return true;
    }
}