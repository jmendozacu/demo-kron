<?php

/**
 * @package    Magedevgroup_ScrappageScheme
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */
class Magedevgroup_ScrappageScheme_Block_Adminhtml_Widget_Button extends Mage_Adminhtml_Block_Widget_Button
{

    protected function _toHtml()
    {
        $html = '<form id="target" action="' . Mage::helper("adminhtml")->getUrl('adminhtml/scrappage/upload/')
            . '?isAjax=true" enctype="multipart/form-data" method="POST">'
            . '<input type="button" class="form-button" id="loadFileXml" value="' . $this->getLabel() . '"'
            . 'onclick="document.getElementById(\'' . $this->getClass() . '\').click();" />'
            . '<input name="file_path" type="' . $this->getType() . '" id="' . $this->getClass() . '" style="display:none;" />'
            . '<input name="form_key" type="hidden" value="' . Mage::getSingleton("core/session")->getFormKey() . '">'
            . '</form>'

            . '<script>'
            . 'Event.observe(document.getElementById(\'' . $this->getClass() . '\'),\'change\', function(){'
            . '$(\'target\').submit();'
            . '});'
            . '</script>';
        return $html;
    }
}
