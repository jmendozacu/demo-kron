<?php

class Sp_Pay4leter_Block_Form_Redirect extends Mage_Core_Block_Abstract {

    protected function _toHtml() {

        
        $pay4later = Mage::getModel('sp_pay4leter/checkoutpay4leter');
        $form = new Varien_Data_Form();

        $form->setAction($pay4later->getPay4laterUrl())
                ->setName('redirect')
                ->setMethod('post')
                ->setUseContainer(true);

        foreach ($pay4later->getStandardCheckoutFormFields('redirect') as $field => $value) {
        
            $form->addField($field, 'hidden', array('name' => $field, 'value' => $value));
        }
    
		$html = '<html>
				<body style="text-align:center;">';
        $html.= $this->__('You will be redirected to pay4later in a few seconds.<br /><center>');
        $html.='<img src="' . $this->getSkinUrl('pay4later/pay4later_bz.jpg') . '" border="1" alt="Logo" width="185px" height="70px" /><br /><br />';
        $html.= '<img src="' . $this->getSkinUrl('pay4later/ajax-loader.gif') . '" alt="ajax-loader" align="center" width="128px" height="15px" /><br /></center>';
        //$html.= $this->__('');
        $html.= $form->toHtml();
        $html.= '<script type="text/javascript">
	   			  function formsubmit()
				  {
				  	document.redirect.submit();	
				  }
					setTimeout("formsubmit()", 3000);
	            </script>';
            //echo $html;exit;
        $html.= '</body></html>';
       
        return $html;
    }

}