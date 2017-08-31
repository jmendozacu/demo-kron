<?php
/**
 * Velan Info Services India Pvt Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.velanapps.com/License.txt
 *
  /***************************************
 *         MAGENTO EDITION USAGE NOTICE *
 * *************************************** */
/* This package designed for Magento COMMUNITY edition
 * Velan Info Services does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Velan Info Services does not provide extension support in case of
 * incorrect edition usage.
  /***************************************
 *         DISCLAIMER   *
 * *************************************** */
/* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 * ****************************************************
 * @category   velanapps
 * @package    EzLogin
 * @author     Velan Team
 * @copyright  Copyright (c) 2012 - 2013 Velan Info Services India Pvt Ltd. (http://www.velanapps.com)
 * @license    http://store.velanapps.com/License.txt
 */
class Velanapps_Ezlogin_Model_Ezlogincustomers extends Mage_Core_Model_Abstract
{
	/**
	Function for Ezlogin Customer Table Collection.
	Input  : Being called, no specific input given.
    Output : Returns Ezlogin Customer Table Data.	
	*/
    public function _construct()
    {
        $this->_init('ezlogin/ezlogincustomers');
    }
}