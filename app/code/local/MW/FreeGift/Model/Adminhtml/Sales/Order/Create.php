<?php
class MW_FreeGift_Model_Adminhtml_Sales_Order_Create extends Mage_Adminhtml_Model_Sales_Order_Create
{
    protected function _prepareOptionsForRequest($item)
    {
        $newInfoOptions = array();
        if ($optionIds = $item->getOptionByCode('option_ids')) {
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
            	if($optionId =='freegift'){continue;}
                $option = $item->getProduct()->getOptionById($optionId);
                $optionValue = $item->getOptionByCode('option_'.$optionId)->getValue();

                $group = Mage::getSingleton('catalog/product_option')->groupFactory($option->getType())
                    ->setOption($option)
                    ->setQuoteItem($item);

                $newInfoOptions[$optionId] = $group->prepareOptionValueForRequest($optionValue);
            }
        }
        return $newInfoOptions;
    }
}
