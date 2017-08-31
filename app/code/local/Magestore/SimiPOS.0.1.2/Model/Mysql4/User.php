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
 * Simipos User Resource Model
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Mysql4_User extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('simipos/user', 'user_id');
    }
    
    public function userExists(Mage_Core_Model_Abstract $user)
    {
        $select = $this->_getReadAdapter()->select();
        $select->from(array('main_table' => $this->getMainTable()));
        $select->where("(username = '{$user->getUsername()}' OR username = '{$user->getEmail()}' OR email = '{$user->getEmail()}' OR email = '{$user->getUsername()}') AND user_id != '{$user->getId()}'");
        return $this->_getReadAdapter()->fetchRow($select);
    }
    
    public function loadByUsername($username)
    {
        $select = $this->_getReadAdapter()->select();
        $select->from(array('main_table' => $this->getMainTable()))
            ->where('username=:username OR email=:username');
        return $this->_getReadAdapter()->fetchRow($select, array('username' => $username));
    }
    
    public function cleanOldSessions(Magestore_SimiPOS_Model_User $user)
    {
        $readAdapter    = $this->_getReadAdapter();
        $writeAdapter   = $this->_getWriteAdapter();
        $timeout        = Mage::getStoreConfig('simipos/general/session_timeout');
        $writeAdapter->delete(
            $this->getTable('simipos/session'),
            $readAdapter->quoteInto('user_id = ?', $user->getId()) . ' AND '
            . new Zend_Db_Expr('(UNIX_TIMESTAMP(\'' . now() . '\') - UNIX_TIMESTAMP(logdate)) > ' . $timeout)
        );
        return $this;
    }
    
    public function recordSession(Magestore_SimiPOS_Model_User $user)
    {
        $readAdapter    = $this->_getReadAdapter();
        $writeAdapter   = $this->_getWriteAdapter();
        $select = $readAdapter->select()
            ->from($this->getTable('simipos/session'), 'user_id')
            ->where('user_id = ?', $user->getId())
            ->where('sessid = ?', $user->getSessid());
        $loginDate = now();
        if ($readAdapter->fetchRow($select)) {
            $writeAdapter->update(
                $this->getTable('simipos/session'),
                array ('logdate' => $loginDate),
                $readAdapter->quoteInto('user_id = ?', $user->getId()) . ' AND '
                . $readAdapter->quoteInto('sessid = ?', $user->getSessid())
            );
        } else {
            $writeAdapter->insert(
                $this->getTable('simipos/session'),
                array(
                    'user_id' => $user->getId(),
                    'logdate' => $loginDate,
                    'sessid'  => $user->getSessid()
                )
            );
        }
        return $this;
    }
    
    public function loadBySessId($sessId)
    {
        $result = array();
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getTable('simipos/session'))
            ->where('sessid = ?', $sessId);
        if ($session = $adapter->fetchRow($select)) {
            $selectUser = $adapter->select()
                ->from(array('main_table' => $this->getMainTable()))
                ->where('user_id = ?', $session['user_id']);
            if ($user = $adapter->fetchRow($selectUser)) {
                $result = array_merge($user, $session);
            }
        }
        return $result;
    }
    
    public function clearBySessId($sessid)
    {
        $this->_getWriteAdapter()->delete(
            $this->getTable('simipos/session'),
            $this->_getReadAdapter()->quoteInto('sessid = ?', $sessid)
        );
        return $this;
    }
    
    public function recordQuoteId($session)
    {
        $readAdapter    = $this->_getReadAdapter();
        $this->_getWriteAdapter()->update(
            $this->getTable('simipos/session'),
            array('quote_id' => $session->getData('quote_id')),
            $readAdapter->quoteInto('user_id = ?', $session->getUser()->getId()) . ' AND '
                . $readAdapter->quoteInto('sessid = ?', $session->getSessionId())
        );
    }
    
    public function getQuoteId($session)
    {
        $readAdapter    = $this->_getReadAdapter();
        $select = $readAdapter->select();
        $select->from(array('main_table' => $this->getTable('simipos/session')), array('quote_id'))
            ->where('user_id = ?', $session->getUser()->getId())
            ->where('sessid = ?', $session->getSessionId());
        return $readAdapter->fetchOne($select);
    }
}
