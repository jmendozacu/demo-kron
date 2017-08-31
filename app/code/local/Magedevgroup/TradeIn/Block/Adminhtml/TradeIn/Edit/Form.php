<?php

/**
 * @package    Magedevgroup_TradeIn
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */
class Magedevgroup_TradeIn_Block_Adminhtml_TradeIn_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(
            [
                'id' => 'edit_form',
                'action' => $this->getUrl(
                    '*/*/edit',
                    [
                        '_current' => true,
                        'continue' => 0,
                    ]
                ),
                'method' => 'post',
            ]
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset(
            'general',
            array(
                'legend' => $this->__('Proposal Data')
            )
        );

        $product = $this->getProduct($this->getSet()->getCurrentProduct());
        if(is_object($product) && $product->getId()){
            Mage::app()->setCurrentStore('default');
            $fieldset->addField('link', 'link', array(
                'name'      => 'link name',
                'href'      => Mage::helper('catalog/product')->getProductUrl($product->getId()),
                'value'     => $product->getName(),
                'label'     => $this->__('Customer Want To Buy'),
            ));
            Mage::app()->setCurrentStore('admin');
        }

        /** TODO  Add all data */
        $this->addFieldsToFieldset(
            $fieldset, [
                'fname' => [
                    'label' => $this->__('First Name'),
                    'input' => 'text',
                ],
                'sname' => [
                    'label' => $this->__('Second Name'),
                    'input' => 'text',
                ],
                'phone' => [
                    'label' => $this->__('Phone'),
                    'input' => 'text',
                ],
                'mail' => [
                    'label' => $this->__('E-mail'),
                    'input' => 'text',
                ],
                'brand' => [
                    'label' => $this->__('Brand'),
                    'input' => 'text',
                ],
                'model' => [
                    'label' => $this->__('Model'),
                    'input' => 'text',
                ],
                'condition' => [
                    'label' => $this->__('Condition'),
                    'name' => 'setData[condition]',
                    'input' => 'select',
                    'value' => $this->getSet()->getCondition(),
                    'values' => Mage::getModel('magedevgroup_tradein/entity_attribute_source_condition')->toOptionArray(),
                ],
                'age' => [
                    'label' => $this->__('Age'),
                    'input' => 'text',
                ],
            ]
        );

        foreach (json_decode($this->getSet()->getPhoto()) as $key => $photo) {
            $fieldset->addField('photo' . $key, 'image', [
                'label' => $this->__('Image #' . ($key+1)),
                'name' => 'photo' . $key,
                'disabled' => true,
                'value' => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'tradein' . $photo,

            ]);
        }

        $fieldset->addField('packing', 'checkbox', array(
            'label' => $this->__('Original Boxes & Packaging'),
            'name' => 'setData[packing]',
            'value' => ($this->getSet()->getPacking() > 0) ? "1" : "0",
            'checked' => ($this->getSet()->getPacking() > 0) ? "checked" : "",

        ));

        $fieldset->addField('remote', 'checkbox', array(
            'label' => $this->__('Remote'),
            'name' => 'setData[remote]',
            'value' => ($this->getSet()->getRemote() > 0) ? "1" : "0",
            'checked' => ($this->getSet()->getRemote() > 0) ? "checked" : "",

        ));

        $fieldset->addField('instructions', 'checkbox', array(
            'label' => $this->__('Instructions'),
            'name' => 'setData[instructions]',
            'value' => ($this->getSet()->getInstructions() > 0) ? "1" : "0",
            'checked' => ($this->getSet()->getInstructions() > 0) ? "checked" : "",

        ));

        $fieldset->addField('receipt', 'checkbox',
            array(
                'label' => $this->__('Receipt'),
                'name' => 'setData[receipt]',
                'value' => ($this->getSet()->getReceipt() > 0) ? "1" : "0",
                'checked' => ($this->getSet()->getReceipt() > 0)  ? "checked" : "",
            )
        );

        $this->addFieldsToFieldset(
            $fieldset, [
                'comment' => [
                    'label' => $this->__('Comment'),
                    'input' => 'text',
                ],
                'pay_type' => [
                    'label' => $this->__('Payment Type'),
                    'input' => 'select',
                    'value' => $this->getSet()->getPayType(),
                    'values' => Mage::getModel('magedevgroup_tradein/entity_attribute_source_paytype')->toOptionArray(),
                    'disabled' => true,
                ],
            ]
        );

        //Show Pay4Later Options [months & deposit]
        if($this->getSet()->getPayType()=="2"){
            $this->addFieldsToFieldset(
                $fieldset, [
                    'pay4later_months' => [
                        'label' => $this->__('Pay4Later months'),
                        'input' => 'text',
                        'value' => $this->getSet()->getPay4laterMonths(),
                        'disabled' => true,
                    ],
                    'pay4later_deposit' => [
                        'label' => $this->__('Pay4Later deposit, %'),
                        'input' => 'text',
                        'value' => $this->getSet()->getPay4laterDeposit(),
                        'disabled' => true,
                    ],
                ]
            );
        }

        $fieldset->addField('created_at', 'datetime', array(
                'label' => $this->__('Date of Proposal'),
                'name' => 'setData[created_at]',
                'disabled' => true,
                'image' => $this->getSkinUrl('images/grid-cal.gif'),
                'format' => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
                'value' => strtotime($this->getSet()->getCreatedAt())
            )
        );

        $this->addFieldsToFieldset(
            $fieldset, [
                'tradein_status' => [
                    'label' => $this->__('Status of TradeIn Proposal'),
                    'input' => 'select',
                    'value' => $this->getSet()->getTradeinStatus(),
                    'values' => Mage::getModel('magedevgroup_tradein/entity_attribute_source_status')->toOptionArray(),
                    'disabled' => true,
                ],
            ]
        );

        //Discount Type
        $fieldset->addField('discount_type', 'select', array(
            'label' => Mage::helper('tradein')->__('Discount Type'),
            'name' => 'setData[discount_type]',
            'value' => $this->getSet()->getDiscountType(),
            'values' => Mage::getModel('magedevgroup_tradein/entity_attribute_source_discountType')->toOptionArray(),
            'required' => true,
        ));

        $fieldset->addField('discount_amount', 'text', array(
            'label' => Mage::helper('tradein')->__('Discount Amount'),
            'value' => $this->getSet()->getDiscountAmount(),
            'name' => 'setData[discount_amount]',
            'required' => true,
            'class' => 'input-text required-entry validate-number'
        ));
    }

    /**
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param $fields
     * @return $this
     */
    protected function addFieldsToFieldset(Varien_Data_Form_Element_Fieldset $fieldset, $fields)
    {
        $requestData = new Varien_Object(
            $this->getRequest()->getPost('setData')
        );

        foreach ($fields as $name => $_data) {
            if ($requestValue = $requestData->getData($name)) {
                $_data['value'] = $requestValue;
            }
            $_data['name'] = "setData[$name]";

            $_data['title'] = $_data['label'];

            if (!array_key_exists('value', $_data)) {
                $_data['value'] = $this->getSet()->getData($name);
            }

            $fieldset->addField($name, $_data['input'], $_data);
        }

        return $this;
    }

    /**
     * @return false|Magedevgroup_TradeIn_Model_Resource_TradeInProposal_Collection
     */
    protected function getSet()
    {
        /** @var Magedevgroup_TradeIn_Model_Resource_TradeInProposal_Collection $collection */
        $proposal = Mage::getModel('magedevgroup_tradein/tradeInProposal');
        if ($proposalId = $this->getRequest()->getParam('id', false)) {
            $proposal->load($proposalId);
        }
        $this->setData('set', $proposal);

        return $this->getData('set');
    }

    /**
     * @param $productId
     * @return Mage_Catalog_Model_Product
     */
    protected function getProduct($productId)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('catalog/product')->load($productId);

        return $product;
    }
}
