<?php

class MW_FreeGift_Block_Sales_Order_Item_Renderer_Default extends Mage_Sales_Block_Order_Item_Renderer_Default
{
    public function getItemOptions()
    {
    		$infoBuyRequest = $this->getOrderItem()->getProductOptionByCode('info_buyRequest');
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
			    
				$options = array_merge($options, $this->getOrderItem()->getProductOptions());
    		}else{
    			$options = $this->getOrderItem()->getProductOptions();
    		}
    		
    	$result = array();
        if ($options) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }
        return $result;
    }
}
