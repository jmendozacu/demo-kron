<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * @category   Sp
 * @package    Sp_Pay4later
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Sp_Pay4leter_Block_Info_Pay4leter extends Mage_Payment_Block_Info
{
    /**
     * Init default template for block
     */
    public $pay4leterConfigArr;
    public $apiKey;
    public $isActive;
    public $rankByKey;

    protected function _construct()
    {
       parent::_construct();
       $this->setTemplate('pay4leter/info.phtml');
       //return parent::_toHtml();
    }

    /**
     * Retrieve credit card type name
     *
     * @return string
     */
    public function getPay4leterTypeName()
    {
    	
        $pay4laterArray = Mage::getStoreConfig('payment');
    	$finalArr=array();

        //print_r($pay4laterArray);exit;
    	$this->apiKey = $pay4laterArray['tab_required_settings']['pay4leter_api_key'];
    	$this->isActive = $pay4laterArray['tab_required_settings']['active'];

    	$this->loanvalue = $pay4laterArray['tab_global_configs']['loanvalue'];

    }
    public function getFirstPercentage($key){
       
       echo $key;exit;
       $fromDbModel = Mage::getModel('sp_pay4leter/pay4leter')->getCollection();
        $plancodes = $fromDbModel->addFieldToFilter('main_table.plan_name', array( $key))
                ->load();
        if($plancodes->getData()){

            $value = Mage::getStoreConfig('payment/dynamic_plans');
            $value = Mage::helper('pay4leter/pay4laterplans')->_unserializeValue($value['dynamic_pay4later_plans']);
            
            foreach($value as $v){
                if($v['plan_code'] == $key){
                    return $v['deposite_1'];
                    break;
                }
            }
        }
    }


    
}
 ?>