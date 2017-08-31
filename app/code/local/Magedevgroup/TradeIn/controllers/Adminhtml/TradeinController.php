<?php

/**
 * @package    Magedevgroup_TradeIn
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */
class Magedevgroup_TradeIn_Adminhtml_TradeinController extends Mage_Adminhtml_Controller_Action
{

    //TradeIn Proposal Status
    const WAIT = 0;
    const DECLINE = 1;
    const ACCEPT = 2;
    const COUPSEND = 3;
    const SHIPSEND = 4;

    protected $checkboxes = array(
        'packing',
        'remote',
        'instructions',
        'receipt'
    );

    /**
     * View list of Proposals in grid
     */
    public function indexAction()
    {
        $tradeinBlock = $this->getLayout()
            ->createBlock('tradein_adminhtml/tradeIn');

        $this->loadLayout()
            ->_addContent($tradeinBlock)
            ->renderLayout();
    }

    /**
     * View certain Proposal
     */
    public function editAction()
    {
        $tradeinEditBlock = $this->getLayout()
            ->createBlock('tradein_adminhtml/tradeIn_edit');

        $this->loadLayout()
            ->_addContent($tradeinEditBlock)
            ->renderLayout();

        $id = $this->getRequest()->getParam('id', false);

        if ($postData = $this->getRequest()->getPost('setData')) {
            try {
                /** @var Mage_Core_Model_Session $session */
                $session = $this->_getSession();

                /** @var Magedevgroup_TradeIn_Model_Resource_TradeInProposal_Collection $collection */
                $proposal = Mage::getModel('magedevgroup_tradein/tradeInProposal');
                if ($id) {
                    $proposal->load($id);
                }
                $proposal->addData($postData);

                foreach ($this->checkboxes as $checkbox) {
                    if (!array_key_exists($checkbox, $postData)) {
                        $proposal->setData($checkbox, 0);
                    } else {
                        $proposal->setData($checkbox, 1);
                    }
                }

                /** @var Mage_Catalog_Model_Product $product */
                $product = Mage::getModel('catalog/product')->load($proposal->getCurrentProduct());
                if ($proposal->getDiscountType()==0 && $proposal->getDiscountAmount() > $product->getPrice()) {//DISCOUNT AMOUNT
                    $session->addError($this->__('Proposal [ID:' . $proposal->getId() . '] can not been Save. Discount amount higher of Product price.'));

                    return $this->_redirectReferer();
                }
                if ($proposal->getDiscountType()==1 && $proposal->getDiscountAmount() > 100) {//DISCOUNT PERCENTAGE
                    $session->addError($this->__('Proposal [ID:' . $proposal->getId() . '] can not been Save. Discount percentage higher of 100%.'));

                    return $this->_redirectReferer();
                }

                $proposal->save();

                $session->addSuccess($this->__('Proposal [ID:' . $proposal->getId() . '] has been saved.'));
                return $this->_redirect('*/*/index');
            } catch
            (Exception $e) {
                Mage::logException($e);
                $session->addError($e->getMessage());
            }
        }
    }

    /**
     * Accept Proposal & send e-mail to customer
     */
    public function acceptAction()
    {
        $id = $this->getRequest()->getParam('id', false);

        try {
            /** @var Magedevgroup_TradeIn_Model_Resource_TradeInProposal_Collection $collection */
            $proposal = Mage::getModel('magedevgroup_tradein/tradeInProposal');
            if ($id) {
                $proposal->load($id);
            }

            if (in_array($proposal->getTradeinStatus(), array(self::ACCEPT, self::COUPSEND, self::SHIPSEND))) {
                $this->_getSession()->addError($this->__('Proposal [ID:' . $proposal->getId() . '] can not been Accepted. The proposal was be accepted earlier.'));

                return $this->_redirect('*/*/index');
            }

            if ($proposal->getDiscountAmount() == null) {
                $this->_getSession()->addError($this->__('Proposal [ID:' . $proposal->getId() . '] can not been Accepted. Not set "Discount amount".
                Set "Discount amount" & Save it before accepted.'));

                return $this->_redirect('*/*/index');
            }

            //change status to 'Accept'
            $proposal->setTradeinStatus(self::ACCEPT);

            $proposal->save();

            $this->_getSession()->addSuccess($this->__('Proposal [ID:' . $proposal->getId() . '] has been Accepted.'));
        } catch
        (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
        }

        return $this->_redirect('*/*/index');
    }

    /**
     * Decline Proposal from customer
     */
    public function declineAction()
    {
        $id = $this->getRequest()->getParam('id', false);

        try {
            /** @var Magedevgroup_TradeIn_Model_Resource_TradeInProposal_Collection $collection */
            $proposal = Mage::getModel('magedevgroup_tradein/tradeInProposal');
            if ($id) {
                $proposal->load($id);
            }

            if ($proposal->getTradeinStatus() != self::WAIT) {
                $this->_getSession()->addError($this->__('Proposal [ID:' . $proposal->getId() . '] can not been Declined.'));

                return $this->_redirect('*/*/index');
            }

            //change status to 'Decline'
            $proposal->setTradeinStatus(self::DECLINE);

            $proposal->save();

            $this->_getSession()->addSuccess($this->__('Proposal [ID:' . $proposal->getId() . '] has been Declined.'));
        } catch
        (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
        }

        return $this->_redirect('*/*/index');
    }
}
