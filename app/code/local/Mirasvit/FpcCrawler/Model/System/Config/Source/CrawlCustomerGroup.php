<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Full Page Cache
 * @version   1.0.32
 * @build     662
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_FpcCrawler_Model_System_Config_Source_CrawlCustomerGroup
{
    public function toOptionArray()
    {
        $groupCollection = Mage::getModel('customer/group')->getCollection();
        $result = array();
        foreach($groupCollection as $group) {
            $customerGroupId = $group->getCustomerGroupId();
            $customerGroupCode = $group->getCustomerGroupCode();
            if ($customerGroupId) {
                $result[] = array('value'=>$customerGroupId, 'label'=>$customerGroupCode);
            }
        }

        return $result;
    }
}
