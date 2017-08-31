<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * SimiPOS Magestore Server Helper
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Helper_Magestore extends Mage_Core_Helper_Abstract
{
    protected function saveConfig($field, $value)
    {
        Mage::getConfig()->saveConfig('simipos/account/' . $field, $value);
    }
	
    /**
     * @return Magestore_SimiPOS_Model_Magestore
     */
    protected function _magestore()
    {
    	return Mage::getSingleton('simipos/magestore');
    }
    
    public function getStoreUrl()
    {
    	return Mage::app()->getStore()->getBaseUrl(
            Mage_Core_Model_Store::URL_TYPE_LINK,
            Mage::getStoreConfigFlag('web/secure/use_in_adminhtml')
        );
    }
    
    public function refineUrl($url)
    {
    	$storeUrl = $this->getStoreUrl();
    	if (!preg_match("/^http\:\/\/|https\:\/\//",$url)) {
    		if (strpos($storeUrl, 'https://')) {
    			$url = 'https://' . $url;
    		} else {
    			$url = 'http://' . $url;
    		}
    	}
    	if ($pos = strpos($url, '/admin')) {
    		$url = substr($url, 0, $pos);
    	}
    	if ($pos = strpos($url, '/simipos')) {
    		$url = substr($url, 0, $pos);
    	}
    	if ($pos = strpos($url, '/index.php')) {
    		if (strpos($storeUrl, '/index.php')) {
    			$url = substr($url, 0, $pos + 10);
    		} else {
    			$url = substr($url, 0, $pos);
    		}
    	}
    	return trim($url, '/') . '/';
    }
    
	/**
	 * Login by main Account
	 * 
	 * @param string $username
	 * @param string $password
	 * @return string
	 */
	public function login($username, $password)
	{
		if (!$username || !$password) {
			throw new Exception($this->__('Username or password cannot be empty'));
		}
		$result = $this->_magestore()->login($username, $password);
		if ($result->getIsSubaccount()) {
		  throw new Exception($this->__('Please using your main account'));
		}
		if (!$result->getStatus()) {
		  throw new Exception($this->__('Your account is inactive. Please contact us to reactivate it.'));
		}
		// Update main account
		$mainAccount = Mage::getModel('simipos/user')->loadByUsername($username);
		if (!$mainAccount->getId() && $result->getApiKey()) {
			$mainAccount->setEmail($username)
			    ->setFirstName($result->getFirstName())
			    ->setLastName($result->getLastName())
			    ->setStatus(Magestore_SimiPOS_Model_Status::STATUS_ACTIVE)
			    ->setPassword($password)
			    ->setUserRole(Magestore_SimiPOS_Model_Role::ROLE_ADMIN);
			try {
				$mainAccount->save();
			} catch (Exception $e) {}
		}
		$mode = Mage::app()->getRequest()->getPost('mode', null);
		if ($mode == null) {
			$mode = Mage::getStoreConfig('simipos/account/mode');
		} else {
			$this->saveConfig('mode', $mode);
		}
		
		$termsOptions = array();
		if (is_array($result->getTerms()))
		foreach ($result->getTerms() as $id => $data) {
			$termLabel = null;
			if ($mode) {
			  if (!empty($data['dev_domain'])) {
			     $termLabel = $this->refineUrl($data['dev_domain']);
			  } elseif ($data['status'] == 1) {
			  	 $activePlan = $id;
			  }
			} else {
			  if (!empty($data['live_domain'])) {
			     $termLabel = $this->refineUrl($data['live_domain']);
			  } elseif ($data['status'] == 1) {
			  	 $activePlan = $id;
			  }
			}
			if ($termLabel && $termLabel == $this->getStoreUrl()) {
				$activePlan = $id;
				$isSetted = true;
			}
			if ($data['type'] == 2) {
				// Trial
				if (empty($termLabel)) {
					$termLabel = $this->__('Trial to %s',
					   Mage::helper('core')->formatDate($data['expire_on'], 'medium')
					);
					if ($data['status'] == 2) {
					   $termLabel .= ' (' . $this->__('Expired') . ')';
					}
				} else {
					$termLabel .= ' (' . $this->__('Trial to %s',
					   Mage::helper('core')->formatDate($data['expire_on']), 'medium'
					);
				    if ($data['status'] == 2) {
                       $termLabel .= ' - ' . $this->__('Expired');
                    }
					$termLabel .= ')';
				}
			} else {
			    // Commercial
                if (empty($termLabel)) {
                    $termLabel = $this->__('Commercial to %s',
                       Mage::helper('core')->formatDate($data['expire_on'], 'medium')
                    );
                    if ($data['status'] == 2) {
                       $termLabel .= ' (' . $this->__('Expired') . ')';
                    }
                } else {
                    $termLabel .= ' (' . $this->__('Commercial to %s',
                       Mage::helper('core')->formatDate($data['expire_on']), 'medium'
                    );
                    if ($data['status'] == 2) {
                       $termLabel .= ' - ' . $this->__('Expired');
                    }
                    $termLabel .= ')';
                }
			}
            $termsOptions[] = array(
                'value' => $id,
                'label' => $termLabel
            );
		}
		if (count($termsOptions) == 1 && !empty($activePlan)) {
			$data = $result->getData('terms/' . $activePlan);
			if ($mode) {
			     $termLabel = $this->__('DEVELOPMENT') . ': ';
			} else {
				$termLabel = '';
			}
		    if ($data['type'] == 2) {
                $termLabel .= $this->__('Trial to %s',
                   Mage::helper('core')->formatDate($data['expire_on'], 'medium')
                );
                if ($data['status'] == 2) {
                   $termLabel .= ' (' . $this->__('Expired') . ')';
                }
            } else {
                $termLabel .= $this->__('Commercial to %s',
                   Mage::helper('core')->formatDate($data['expire_on'], 'medium')
                );
                if ($data['status'] == 2) {
                   $termLabel .= ' (' . $this->__('Expired') . ')';
                }
            }
            $this->saveConfig('username', $username);
            $this->saveConfig('api_key', $result->getApiKey());
            $this->saveConfig('term_id', $activePlan);
            Mage::getConfig()->cleanCache();
			if (empty($isSetted)) {
			    // Query to reset term_id (update for Magestore Server)
			    try {
			        $this->_magestore()->updatePackage();
			    } catch (Exception $e) {
			        $this->saveConfig('term_id', '');
			        throw new Exception($e->getMessage());
			    }
			}
			// Sync Sub Accounts
			try {
				$accountList = $this->_magestore()->accountList();
				$existAccounts = Mage::getResourceModel('simipos/user_collection')
				    ->addFieldToFilter('email', array('neq' => $username));
				$mEmails = array();
				foreach ($accountList as $accountData) {
					$isNewAccount = true;
					foreach ($existAccounts as $account) {
						if ($accountData['email'] == $account->getEmail() && $account->getEmail()) {
							$isNewAccount = false;
							// Check password is equal
							if ($accountData['password'] != $account->getPassword()) {
								try {
									$account->addData($accountData)
									   ->setPasswordHash($accountData['password'])
									   ->save();
								} catch (Exception $e) {}
							}
						}
					}
				    if ($isNewAccount) {
				    	// Create New Subaccount
				    	try {
				    	   $model = Mage::getModel('simipos/user')
				    	       ->setData($accountData);
				    	   $model->setPasswordHash($model->getPassword());
				    	   $model->setId(null)->save();
				    	} catch (Exception $e) {}
				    }
				    $mEmails[] = $accountData['email'];
				}
				$accountList = array();
				foreach ($existAccounts as $account) {
					if ($account->getEmail() && !in_array($account->getEmail(), $mEmails)) {
						// Upload account to Magestore
						try {
							$accountData = $account->getData();
							// $accountData['password_hash'] = $account->getPassword();
							unset($accountData['role_permission']);
                            unset($accountData['created_time']);
                            unset($accountData['username']);
							$accountData['term_id'] = Mage::getStoreConfig('simipos/account/term_id');
							$accountList[] = $accountData;
							// $this->_magestore()->accountCreate($accountData);
						} catch (Exception $e) {}
					}
				}
				if (count($accountList)) {
					try {
						$this->_magestore()->accountUpdateBlock($accountList);
					} catch (Exception $e) {}
				}
			} catch (Exception $e) {
				// Do Nothing
			}
			
			// Save Term Data
            $this->saveConfig('term_description', $termLabel);
            $this->saveConfig('expire_on', $data['expire_on']);
			$this->saveConfig('term_options', '');
		} else if (count($termsOptions)) {
			$this->saveConfig('term_options', serialize($termsOptions));
		} else {
		    $this->saveConfig('term_options', '');
		    throw new Exception($this->__('You have not purchased SimiPOS package. Please purchase one or use the trial.'));
		}
		
		// Return API Key
		return $result->getApiKey();
	}
	
	public function changePackage($packageId)
	{
        $this->saveConfig('term_id', $packageId);
        Mage::getConfig()->cleanCache();
        try {
        	$data = $this->_magestore()->packageInfo();
            $this->_magestore()->updatePackage();
        } catch (Exception $e) {
            $this->saveConfig('term_id', '');
            throw new Exception($e->getMessage());
        }
        // Sync Sub Accounts
        try {
           $accountList = $this->_magestore()->accountList();
           $existAccounts = Mage::getResourceModel('simipos/user_collection')
               ->addFieldToFilter('email', array('neq' => Mage::getStoreConfig('simipos/account/username')));
           $mEmails = array();
           foreach ($accountList as $accountData) {
               $isNewAccount = true;
               foreach ($existAccounts as $account) {
                   if ($accountData['email'] == $account->getEmail() && $account->getEmail()) {
                       $isNewAccount = false;
                       // Check password is equal
                       if ($accountData['password'] != $account->getPassword()) {
                          try {
                             $account->addData($accountData)
                                ->setPasswordHash($accountData['password'])
                                ->save();
                          } catch (Exception $e) {}
                       }
                    }
               }
               if ($isNewAccount) {
                  // Create New Subaccount
                  try {
                     $model = Mage::getModel('simipos/user')
                         ->setData($accountData);
                     $model->setPasswordHash($model->getPassword());
                     $model->setId(null)->save();
                  } catch (Exception $e) {}
               }
               $mEmails[] = $accountData['email'];
           }
           $accountList = array();
           foreach ($existAccounts as $account) {
               if ($account->getEmail() && !in_array($account->getEmail(), $mEmails)) {
                   // Upload account to Magestore
                   try {
                       $accountData = $account->getData();
                       // $accountData['password_hash'] = $account->getPassword();
                       unset($accountData['role_permission']);
                       unset($accountData['created_time']);
                       unset($accountData['username']);
                       $accountData['term_id'] = Mage::getStoreConfig('simipos/account/term_id');
                       $accountList[] = $accountData;
                       // $this->_magestore()->accountCreate($accountData);
                   } catch (Exception $e) {}
               }
          }
          if (count($accountList)) {
          	  try {
          	  	  $this->_magestore()->accountUpdateBlock($accountList);
          	  } catch (Exception $e) {}
          }
       } catch (Exception $e) {
          // Do Nothing
       }
       
	   if (Mage::getStoreConfig('simipos/account/mode')) {
            $termLabel = $this->__('DEVELOPMENT') . ': ';
       } else {
            $termLabel = '';
       }
       if ($data['type'] == 2) {
           $termLabel .= $this->__('Trial to %s',
               Mage::helper('core')->formatDate($data['expire_on'], 'medium')
           );
           if ($data['status'] == 2) {
             $termLabel .= ' (' . $this->__('Expired') . ')';
           }
       } else {
           $termLabel .= $this->__('Commercial to %s',
               Mage::helper('core')->formatDate($data['expire_on'], 'medium')
           );
           if ($data['status'] == 2) {
               $termLabel .= ' (' . $this->__('Expired') . ')';
           }
       }
       
       // Save Term Data
       $this->saveConfig('term_description', $termLabel);
       $this->saveConfig('expire_on', $data['expire_on']);
       Mage::getConfig()->cleanCache();
	}
	
	/**
	 * Update Magestore users (subaccount)
	 * 
	 * @param Magestore_SimiPOS_Model_User $user
	 */
	public function updateUser(Magestore_SimiPOS_Model_User $user)
	{
		if ($user->getData('email') == Mage::getStoreConfig('simipos/account/username')
		  || !Mage::getStoreConfig('simipos/account/username')
		) {
			return ;
		}
		// Check data changed (status, password, email)
		if ($user->getOrigData('email') == $user->getData('email')
		  && $user->getOrigData('status') == $user->getData('status')
		  && $user->getOrigData('password') == $user->getData('password')
		) {
			return ;
		}
		// Update for Magestore
		$accountData = $user->getData();
		try {
			unset($accountData['role_permission']);
			unset($accountData['created_time']);
			unset($accountData['username']);
			$accountData['term_id'] = Mage::getStoreConfig('simipos/account/term_id');
			if ($user->getOrigData('email')
			    && $user->getOrigData('email') != $user->getData('email')
			) {
				$result = $this->_magestore()->accountList($user->getOrigData('email'));
				if (is_array($result) && isset($result[0]) && !empty($result[0]['subaccount_id'])) {
					$this->_magestore()->accountUpdate($result[0]['subaccount_id'], $accountData);
				} else {
					$this->_magestore()->accountCreate($accountData);
				}
			} else {
			    $this->_magestore()->accountUpdateBlock(array($accountData));
			}
		} catch (Exception $e){}
	}
	
	public function deleteUser(Magestore_SimiPOS_Model_User $user)
	{
	    if ($user->getData('email') == Mage::getStoreConfig('simipos/account/username')
	       || !$user->getData('email') || !Mage::getStoreConfig('simipos/account/username')
	    ) {
            return ;
        }
	    try {
            $userExist = $this->_magestore()->accountList($user->getData('email'));
            if (count($userExist) && !empty($userExist[0]['subaccount_id'])) {
            	$this->_magestore()->accountDelete($userExist[0]['subaccount_id']);
            }
        } catch (Exception $e){}
	}
}
