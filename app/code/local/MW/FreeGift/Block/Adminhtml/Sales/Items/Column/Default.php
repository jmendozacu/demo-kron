<?php
class MW_FreeGift_Block_Adminhtml_Sales_Items_Column_Default extends Mage_Adminhtml_Block_Sales_Items_Column_Default
{
    public function getOrderOptions()
    {
        $result = array();
    	$infoBuyRequest = $this->getItem()->getProductOptionByCode('info_buyRequest');
    		if(isset($infoBuyRequest['option']))
    		{
    			$option = unserialize($infoBuyRequest['option']);
    			$_options = array(
                    0=>array(
                        'label' => $option['label'],
                        'value' => $option['value'],
                        'print_value' => $option['value'],
                        /*'option_id' => '1',*/
                        'option_type' => 'text',
                        'custom_view' => true
                    )
				);
			    $options = array('additional_options'=>$_options);
				$options = array_merge($options, $this->getItem()->getProductOptions());
    		}else{
    			$options = $this->getItem()->getProductOptions();
    		}
        if ($options) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (!empty($options['attributes_info'])) {
                $result = array_merge($options['attributes_info'], $result);
            }
        }
        return $result;
    }
}
