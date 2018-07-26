<?php

require_once(
    Mage::getModuleDir('controllers','Mage_ProductAlert').
    DS.'UnsubscribeController.php');
class Potoky_AlertAnonymous_UnsubscribeController extends Mage_ProductAlert_UnsubscribeController
{
    public static $helpers;

    public function preDispatch()
    {
        Mage::helper('alertanonymous')->setUpHelpers($this);
        $unsubscribeInfo = $this->getRequest()->getParam('unsubscribe');
        $customerIdentifiers = explode(
            ' ',
            rawurldecode($unsubscribeInfo)
        );

        $email = $customerIdentifiers[0];
        $websiteId = $customerIdentifiers[1];;

        $customer = self::$helpers['entity']->getCustomerEntityByRequest('customer/customer', $email, $websiteId);
        if ($customer->getId()) {
            self::$helpers['registry']->setRegistry(null, $customer, true);
            parent::preDispatch();
            return;
        }

        $anonymousCustomer = self::$helpers['entity']->getCustomerEntityByRequest('anonymouscustomer/anonymous', $email, $websiteId);
        if ($id = $anonymousCustomer->getId()) {
            self::$helpers['registry']->setRegistry(null, $anonymousCustomer, false);
        }

        Mage_Core_Controller_Front_Action::preDispatch();
    }
}