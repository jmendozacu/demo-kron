<?php
/**
 * User: Anh TO
 * Date: 1/17/14
 * Time: 3:18 PM
 */

class MW_FreeGift_Block_Hidden_Inject_Checkout_Cart_Init extends Mage_Core_Block_Template{
    public function init(){
        $items = Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
        if(count($items) > 0){
            $init = array();
            foreach($items as $item){
                $product = Mage::getSingleton('catalog/product')->load($item->getProduct()->getId());
                $product_name = str_replace("'", "", $product->getName());
                $init[$item->getItemId()] = array(
                    'product_id'                =>  $product->getId(),
                    'product_name'              =>  $product_name,
                    'product_image'             =>  (string)Mage::helper('catalog/image')->init($product, 'image')->resize(265, 265),
                    'product_has_options'       =>  ($product->getOptions() ? "1" : "0"),
                    'product_type'              =>  $product->getTypeId(),
                );
            }

            return json_encode($init);
        }

        return "";
    }
}
