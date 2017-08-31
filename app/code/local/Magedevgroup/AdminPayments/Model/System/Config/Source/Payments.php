<?php
/**
 * @package    Magedevgroup_AdminPayments
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */
class Magedevgroup_AdminPayments_Model_System_Config_Source_Payments
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $methods = Mage::helper('payment')->getPaymentMethods();
        foreach ($methods as $methodCode => $methodData) {
            if (isset($methodData['model'])) {
                $methodInstance = Mage::getModel($methodData['model']);
                if (!$methodInstance) {
                    continue;
                }
                if ($methodInstance->canUseInternal()) {
                    if ($methodInstance->getCode() != null && $methodInstance->getTitle() != null) {
                        $internalMethods[] = array(
                            'value' => $methodInstance->getCode(),
                            'label' => $methodInstance->getTitle()
                        );
                    }
                }
            }
        }
        return $internalMethods;
    }

}