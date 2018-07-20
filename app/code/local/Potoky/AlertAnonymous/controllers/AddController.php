<?php

require_once(
    Mage::getModuleDir('controllers','Mage_ProductAlert').
    DS.'AddController.php');
class Potoky_AlertAnonymous_AddController extends Mage_ProductAlert_AddController
{
    public static $helpers;

    public function preDispatch()
    {
        if(!Mage::helper('alertanonymous/allow')->isCurrentAnonymousAlertAllowed()) {
            parent::preDispatch();
            return;
        }
        Mage_Core_Controller_Front_Action::preDispatch();

        if (Mage::helper('alertanonymous/login')->isLoggedIn()) {
            return;
        }

        $email = $this->getRequest()->getParam('email');
        $websiteId = Mage::app()->getWebsite()->getId();

        $customer = Mage::helper('anonymouscustomer/entity')
            ->getCustomerEntityByRequest('customer/customer', $email, $websiteId);
        if ($id = $customer->getId()) {
            Mage::helper('alertanonymous/registry')->setRegistry(null, $customer, true);
            return;
        }
        $anonymousCustomer = Mage::helper('anonymouscustomer/entity')
            ->getCustomerEntityByRequest('anonymouscustomer/anonymous', $email, $websiteId);
        if ($id = $anonymousCustomer->getId()) {
            Mage::helper('alertanonymous/registry')->setRegistry(null, $anonymousCustomer, false);
            return;
        }

        $anonymousCustomer->setWebsiteId($websiteId)
            ->setEmail($email)
            ->setGroupId(0)
            ->setStoreId(Mage::app()->getStore()->getId())
            ->_setCheckIfRegistered(false);
        try {
            $anonymousCustomer->save();
        } catch (Exception $e) {
            Zend_Debug::dump($e->getMessage());
        }

        Mage::helper('alertanonymous/registry')->setRegistry(null, $anonymousCustomer, false);
    }
}