<?php

require_once(
    Mage::getModuleDir('controllers','Mage_ProductAlert').
    DS.'UnsubscribeController.php');
class Potoky_AlertAnonymous_UnsubscribeController extends Mage_ProductAlert_UnsubscribeController
{
    private $customerIdentifiers;

    public function getCustomerIdentifiers()
    {
        return $this->customerIdentifiers;
    }

    public function preDispatch()
    {
        $unsubscribeHash = $this->getRequest()->getParam('anonymous');
        $this->customerIdentifiers = explode(
            ' ',
            Mage::helper('core')->decrypt($unsubscribeHash)
        );


        $email = $this->customerIdentifiers[0];
        $websiteId = $this->customerIdentifiers[1];;
        Mage::unregister('potoky_alertanonymous');

        $customer = Mage::helper('anonymouscustomer/entity')
            ->getCustomerEntityByRequest('customer/customer', $email, $websiteId);
        if ($id = $customer->getId()) {
            Mage::register('potoky_alertanonymous',
                [
                    'id' => $id,
                    'parent_construct' => true,
                    'controller' => $this
                ]
            );
            parent::preDispatch();
            return;
        }

        $anonymousCustomer = Mage::helper('anonymouscustomer/entity')
            ->getCustomerEntityByRequest('anonymouscustomer/anonymous', $email, $websiteId);
        if ($id = $anonymousCustomer->getId()) {
            Mage::register('potoky_alertanonymous',
                [
                    'id' => $id,
                    'parent_construct' => false
                ]
            );
        }

        Mage_Core_Controller_Front_Action::preDispatch();
    }
}