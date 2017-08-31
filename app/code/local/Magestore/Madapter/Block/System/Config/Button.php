<?php

class Magestore_Madapter_Block_System_Config_Button extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element){
        $url = $this->getUrl('madapteradmin/adminhtml_madapter/sendNotice');        
       // $layout  =  Mage::helper('bannerslider')->returnlayout();
        //$block = Mage::helper('bannerslider')->returnblock();
        //$text =  Mage::helper('bannerslider')->returntext();
       // $template = Mage::helper('bannerslider')->returntemplate();
        return '
<div id="send-notice" style="">
    <button onclick="checkSendNotice()" class="scalable save" type="button" id="send-notice">
        <span><span><span id="send-button">Send</span></span></span>
    </button>
</div>
<script type="text/javascript">    
    var check = 0;
    Event.observe($(\'madapter_notice_message\'),\'change\', function(){
        check = 1;
        $(\'send-button\').update(\'Save Config\');
    });
    Event.observe($(\'madapter_notice_title\'),\'change\', function(){
        check = 1;
        $(\'send-button\').update(\'Save Config\');
    });
    Event.observe($(\'madapter_notice_url\'),\'change\', function(){
        check = 1;
        $(\'send-button\').update(\'Save Config\');
    });
    function checkSendNotice(){
        message = $(\'madapter_notice_message\').value;
        title = $(\'madapter_notice_title\').value;
        url = $(\'madapter_notice_url\').value;
        if (message && title && url){
            if (check == 1){
                configForm.submit()
            }else{
                setLocation(\''.$url.'\');
            }            
        }else{
            alert(\'Please fill out message, title and url\');
        }
    }
</script>

';
    }
}
