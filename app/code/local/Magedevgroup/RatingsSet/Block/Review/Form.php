<?php
/**
 * @package    Magedevgroup_RatingsSet
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */

class Magedevgroup_RatingsSet_Block_Review_Form extends Itactica_ExtendedReviews_Block_Form
{

    public function getRatings()
    {
        $productId = Mage::app()->getRequest()->getParam('id', false);
        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('catalog/product')->load($productId);
        $ratingsSet = $product->getRatingsSet();
        if(!$ratingsSet){
            /** @var Magedevgroup_RatingsSet_Helper_Data $helper */
            $helper = Mage::helper('magedevgroup_ratingsset');
            $ratingsSet = $helper->getDefaultSet();
        }

        /** @var Magedevgroup_RatingsSet_Model_Set $collection */
        $collection = Mage::getModel('magedevgroup_ratingsset/set')->load($ratingsSet);

        $ratingCollection = Mage::getModel('rating/rating')
            ->getResourceCollection()
            ->addEntityFilter('product');
        if ($collection->getRatings()) {
            $ratingCollection->addFieldToFilter('main_table.rating_id', array(
                'in' => explode(',', $collection->getRatings())
            ));
        }
        $ratingCollection->setPositionOrder()
            ->addRatingPerStoreName(Mage::app()->getStore()->getId())
            ->setStoreFilter(Mage::app()->getStore()->getId())
            ->load()
            ->addOptionToItems();
        return $ratingCollection;
    }
}
