<?php
/**
 * @package    Magedevgroup_RatingsSet
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */

class Magedevgroup_RatingsSet_Block_Review_List extends Itactica_ExtendedReviews_Block_Product_Review_List {

    public function getRating($id)
    {
        if( !$this->getRatingCollection() ) {
            $ratingCollection = Mage::getModel('rating/rating_option_vote')
                ->getResourceCollection()
                ->setReviewFilter($id)
                ->addRatingInfo(Mage::app()->getStore()->getId())
                ->setStoreFilter(Mage::app()->getStore()->getId())
                ->load();

            $this->setRatingCollection( ( $ratingCollection->getSize() ) ? $ratingCollection : false );
        }

        return $this->getRatingCollection();
    }
}
