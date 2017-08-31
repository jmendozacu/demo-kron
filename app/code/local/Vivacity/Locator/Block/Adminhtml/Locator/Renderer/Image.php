<?php
class Vivacity_Locator_Block_Adminhtml_Locator_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{
     
    public function render(Varien_Object $row)
    {
        $html = '<img ';
        $html .= 'id="' . $this->getColumn()->getId() . '" ';
        $html .= 'src="' .Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."/storeLocator/". $row->getData($this->getColumn()->getIndex()) . '" height="80" width="140"';
        $html .= 'class="grid-image ' . $this->getColumn()->getInlineCss() . '"/>';
        return $html;
    }
}
