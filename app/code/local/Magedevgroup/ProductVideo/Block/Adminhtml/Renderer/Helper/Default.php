<?php
/**
 * @package    Magedevgroup_ProductVideo
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */

class Magedevgroup_ProductVideo_Block_Adminhtml_Renderer_Helper_Default
    extends Varien_Data_Form_Element_Text
{
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->setType('text');
        $this->setExtType('textfield');
    }

    /**
     * @return mixed|string
     */
    public function getHtml()
    {
        $html = parent::getHtml();
        $url = $this->getValue();

        $elementDisabled = $this->getDisabled() == 'disabled';
        $disabled = false;

        if (!$this->getValue() || $elementDisabled || ($this->getValue() == '')) {
            $this->setData('disabled', 'disabled');
            $disabled = true;
        }

        if (!$disabled) {
            $embedUrl = Mage::helper('magedevgroup_productvideo')->convertUrl($url);
            if ($embedUrl != null) {
                $html .= '<iframe width="450" height="253" src="//' . $embedUrl . '" frameborder="0" allowfullscreen>
                </iframe>';
            }
        }
        return $html;
    }

}
