<?php
class MW_FreeGift_Model_Coupon_Codegenerator extends Varien_Object
    implements Mage_SalesRule_Model_Coupon_CodegeneratorInterface
{
    public function generateCode()
    {
        $alphabet = ($this->getAlphabet() ? $this->getAlphabet() : 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
        $lengthMin = ($this->getLengthMin() ? $this->getLengthMin() : 16);
        $lengthMax = ($this->getLengthMax() ? $this->getLengthMax() : 32);
        $length = ($this->getLength() ? $this->getLength() : rand($lengthMin, $lengthMax));
        $result = '';
        $indexMax = strlen($alphabet) - 1;
        for ($i = 0; $i < $length; $i++) {
            $index = rand(0, $indexMax);
            $result .= $alphabet{$index};
        }
        return $result;
    }
    public function getDelimiter()
    {
        return ($this->getData('delimiter') ? $this->getData('delimiter') : '-');
    }
}
