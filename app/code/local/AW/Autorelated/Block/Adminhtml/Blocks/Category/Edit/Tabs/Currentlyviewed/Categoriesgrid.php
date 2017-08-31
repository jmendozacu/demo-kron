<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Autorelated
 * @version    2.4.11
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Autorelated_Block_Adminhtml_Blocks_Category_Edit_Tabs_Currentlyviewed_Categoriesgrid
    extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Categories
{
    protected function _beforeToHtml()
    {
        $this->setTemplate('aw_autorelated/catalog/categories/tree.phtml');
        return $this;
    }

    public function getProduct()
    {
        return Mage::registry('categoryblock_data');
    }
}