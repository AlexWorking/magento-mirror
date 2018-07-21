<?php

require_once(
    Mage::getModuleDir('controllers','Mage_ProductAlert').
    DS.'AddController.php');
class Potoky_AlertAnonymous_AddController extends Mage_ProductAlert_AddController
{
    public static $helpers;

    public function preDispatch()
    {
        Mage::helper('alertanonymous')->setUpHelpers($this);
        if(!self::$helpers['allow']->isCurrentAlertAllowedForAnonymous()) {
            parent::preDispatch();
            return;
        }
        Mage_Core_Controller_Front_Action::preDispatch();

        if (self::$helpers['login']->isLoggedIn()) {
            return;
        }

        $email = $this->getRequest()->getParam('email');
        $websiteId = Mage::app()->getWebsite()->getId();

        $customer = self::$helpers['entity']->getCustomerEntityByRequest('customer/customer', $email, $websiteId);
        if ($customer->getId()) {
            self::$helpers['registry']->setRegistry('add', $customer, true);
            return;
        }
        $anonymousCustomer = self::$helpers['entity']->getCustomerEntityByRequest('anonymouscustomer/anonymous', $email, $websiteId);
        if ($anonymousCustomer->getId()) {
            self::$helpers['registry']->setRegistry('add', $anonymousCustomer, false);
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

        self::$helpers['registry']->setRegistry('add', $anonymousCustomer, false);
    }

}