<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_ThemeOne
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * ThemeOne Index Controller
 * 
 * @category    Magestore
 * @package     Magestore_ThemeOne
 * @author      Magestore Developer
 */
class Simi_Themeone_ApiController extends Simi_Connector_Controller_Action
{
    
    public function get_order_categoriesAction(){
        $data=$this->getData();
        $phone_type=$data->phone_type;
        if($phone_type==null || !isset($phone_type)) $phone_type="phone";
        $information=Mage::getModel('themeone/categories_categories')->getCategories($data,$phone_type);
        $this->_printDataJson($information);
    }
     public function get_order_spotsAction(){
        $data=$this->getData();
        $phone_type=$data->phone_type;
        if($phone_type==null || !isset($phone_type)) $phone_type="phone";
        $information=Mage::getModel('themeone/spotproduct_spot')->getSpotProduct($data,$phone_type);
        $this->_printDataJson($information);
    }
    
      public function get_spot_productsAction(){
        $data=$this->getData(); 
        $information=Mage::getModel('themeone/spotproduct_spot')->getSpotProducts($data);
        $this->_printDataJson($information);
    }
    
    public function getMobile() {

        if (!function_exists('getallheaders')) {

            function getallheaders() {
                $head = array();
                foreach ($_SERVER as $name => $value) {
                    if (substr($name, 0, 5) == 'HTTP_') {
                        $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                        $head[$name] = $value;
                    } else if ($name == "CONTENT_TYPE") {
                        $head["Content-Type"] = $value;
                    } else if ($name == "CONTENT_LENGTH") {
                        $head["Content-Length"] = $value;
                    }
                    
                }
                return $head;
            }

        }		
        $head = getallheaders();
   
        if (isset($head['Mobile-App']))
            return "phone";
        
        if ($_SERVER["HTTP_USER_AGENT"]) {
            $user_agent = $_SERVER["HTTP_USER_AGENT"];
         
			if(Mage::getSingleton('core/session')->getSessionSimipopapp()==null){
				if (strstr($user_agent, 'iPhone') || strstr($user_agent, 'iPod')){
				      return "phone";
				}			
				elseif(strstr($user_agent, 'Android') && eregi('Mobile', $user_agent)){
					return "phone";
				}
                                elseif(strstr($user_agent, 'Android') && !eregi('Mobile', $user_agent)){
                                        return "tablet";
                                }
				elseif(strstr($user_agent, 'iPad')){
					return "tablet";
				}  
             
			}            			
            Mage::getSingleton('core/session')->setSessionSimipopapp(1);
            return "phone";          
        }
        return "phone";
    }

}