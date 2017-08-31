<?php
/**
 * Custom image form element that generates correct thumbnail image URL
 *
 * @author Magento
 */
class MW_SocialGift_Block_Adminhtml_Quote_Edit_Form_Element_Textinlineselect extends Varien_Data_Form_Element_Abstract
{

    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->setType('text');
        $this->setLineCount(2);
    }

    public function getHtmlAttributes()
    {
        return array('type', 'title', 'class', 'style', 'onclick', 'onchange', 'disabled', 'maxlength');
    }

    public function getLabelHtml($suffix = 0)
    {
        return parent::getLabelHtml($suffix);
    }

    /**
     * Get element HTML
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = '';
        $lineCount = $this->getLineCount();
        $lineCount = 1;
        // echo  $this->getName();
        // echo  $this->getTitle();
        // echo  $this->getClass();
        // echo  $this->getStyle();
        // echo  $this->getOtheroption();
        // var_dump($this->getUses_limit());
        $selected = $this->getSelected();

        // for ($i = 0; $i < $lineCount; $i++) {
            if ($this->getRequired()) {
                $this->setClass('input-text required-entry');
            } else {
                $this->setClass('input-text');
            }
            $html .= '<div class="multi-input"><input style="'.$this->getStyle().'" id="' . $this->getHtmlId() . '" name="' . $this->getName()
                . '" value="' . $this->getEscapedUses_limit() . '" '
                . $this->serialize($this->getHtmlAttributes()) . ' />' ;

            $html .= '<select style="width: 100px; " name="uses_limit_by">';
        
            if ($options = $this->getOptions()) {
                foreach ($options as $k => $v) {
                    if ($selected == $k) {
                        $html .= '<option value="'.$k.'" selected>'.$v.'</option>';
                    }else{
                        $html .= '<option value="'.$k.'">'.$v.'</option>';
                    }
                }
            }
            $html .= '</select><br/>';

            // if ($i==0) {
                // $html .= $this->getAfterElementHtml();
            // }
            $html .= '</div>';
        // }
        return $html;
    }

    public function getDefaultHtml()
    {
        echo "@@@"; exit;
        $html = '';
        $lineCount = $this->getLineCount();

        for ($i=0; $i<$lineCount; $i++){
            $html.= ( $this->getNoSpan() === TRUE ) ? '' : '<span class="field-row">'."\n";
            if ($i==0) {
                $html.= '<label for="'.$this->getHtmlId().$i.'">'.$this->getLabel()
                    .( $this->getRequired() ? ' <span class="required">*</span>' : '' ).'</label>'."\n";
                if($this->getRequired()){
                    $this->setClass('input-text required-entry');
                }
            }
            else {
                $this->setClass('input-text');
                $html.= '<label>&nbsp;</label>'."\n";
            }
            $html.= '<input id="'.$this->getHtmlId().$i.'" name="'.$this->getName().'['.$i.']'
                .'" value="'.$this->getEscapedValue($i).'"'.$this->serialize($this->getHtmlAttributes()).' />'."\n";
            if ($i==0) {
                $html.= $this->getAfterElementHtml();
            }
            $html.= ( $this->getNoSpan() === TRUE ) ? '' : '</span>'."\n";
        }
        return $html;
    }

    public function getEscapedUses_limit($index=null)
    {
        $value = $this->getUses_limit();

        if (!is_numeric($value)) {
            return null;
        }

        return number_format($value, 0, null, '');
    }


}