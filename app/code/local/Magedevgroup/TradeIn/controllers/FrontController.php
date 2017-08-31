<?php

/**
 * @package    Magedevgroup_TradeIn
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */
class Magedevgroup_TradeIn_FrontController extends Mage_Core_Controller_Front_Action
{
    const WAIT = 0;//TradeIn Status 'Wait for approval'

    public function submitAction()
    {
        //get data from form
        $data = $this->getRequest()->getParams();

        $proposal = Mage::getModel('magedevgroup_tradein/tradeInProposal');

        foreach ($data as $key => $value) {
            $proposal->setData($key, $value);
        }

        $proposal->setData('tradein_status', self::WAIT);

        if (isset($data[packing])) {
            $proposal->setData('packing', 1);
        } else {
            $proposal->setData('packing', 0);
        }
        if (isset($data[remote])) {
            $proposal->setData('remote', 1);
        } else {
            $proposal->setData('remote', 0);
        }
        if (isset($data[instructions])) {
            $proposal->setData('instructions', 1);
        } else {
            $proposal->setData('instructions', 0);
        }
        if (isset($data[receipt])) {
            $proposal->setData('receipt', 1);
        } else {
            $proposal->setData('receipt', 0);
        }

        $uploadfiles = array();
        foreach (array_keys($_FILES['photo']['name']) as $i) { // loop over 0,1,2,3 etc...
            foreach (array_keys($_FILES['photo']) as $j) { // loop over 'name', 'size', 'error', etc...
                $uploadfiles[$i][$j] = $_FILES['photo'][$j][$i]; // "swap" keys and copy over original array values
            }
        }

        $session = Mage::getSingleton('core/session');
        try {
            $currentfiles = array();

            //load photos
            foreach ($uploadfiles as $photoname) {
                $currentfiles[] = $this->saveUploadPhoto($photoname);
            }

            //save array of path("media/tradein/path[i]") in JSON format
            $proposal->setData('photo', json_encode($currentfiles, JSON_UNESCAPED_SLASHES));
            $proposal->save();
            $session->addSuccess('Thanks! We will get back to you with an offer within 24 hours.');
        } catch (Exception $e) {
            $session->addError('Add Error');
        }

        $this->_redirectReferer();
    }

    /**
     * Upload photos on server
     *
     * @param $photoname string  Name of current photo
     * @return null|string  Path of current photo in "media/tradein"
     */
    private function saveUploadPhoto($photoname)
    {
        $uploader = new Mage_Core_Model_File_Uploader($photoname);
        $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'tif', 'tiff', 'bmp']);
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(true);
        $uploader->setAllowCreateFolders(true);

        if (!$uploader->save(Mage::getBaseDir('media') . '/tradein')) {
            return null;
        }

        return $uploader->getUploadedFileName();
    }


    /**
     * TODO!!!!!!!!!!!! FOR TESTED Magedevgroup_TradeIn_Model_Rule_PriceRule
     */
    public function testRuleAction()
    {
        echo (Mage::getModel('magedevgroup_tradein/rule_priceRule')->createRule(798, 15));
    }

    /**
     * TODO!!!!!!!!!!!! FOR TESTED Magedevgroup_TradeIn_Model_Observer::procAcceptProposal
     */
    public function testProcAcceptAction()
    {
        /** @var Magedevgroup_TradeIn_Model_Cron $model */
        $model = Mage::getModel('magedevgroup_tradein/cron');

        $model->procAcceptProposal();
    }

     /**
     * TODO!!!!!!!!!!!! FOR TESTED Magedevgroup_TradeIn_Model_Coupon_Codegenerator
     */
    public function testCouponAction()
    {
        var_dump(Mage::getModel('magedevgroup_tradein/coupon_codegenerator')
            ->getCouponCode(798, 20.0000));
    }
}
