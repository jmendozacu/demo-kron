<?php
/**
 * @package    Magedevgroup_RatingsSet
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */

class Magedevgroup_RatingsSet_Model_Review extends Mage_Review_Model_Review
{
    public function validate()
    {
        $errors = array();

        if (!Zend_Validate::is($this->getTitle(), 'NotEmpty')) {
            $this->setTitle('');
            //$errors[] = Mage::helper('review')->__('Review summary can\'t be empty');
        }

        if (!Zend_Validate::is($this->getNickname(), 'NotEmpty')) {
            $errors[] = Mage::helper('review')->__('Nickname can\'t be empty');
        }

        if (!Zend_Validate::is($this->getDetail(), 'NotEmpty')) {
            $this->setDetail('');
            //$errors[] = Mage::helper('review')->__('Review can\'t be empty');
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }
}