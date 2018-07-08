<?php

require_once(
    Mage::getModuleDir('controllers','Mage_ProductAlert').
    DS.'AddController.php');
class Potoky_AlertAnonymous_AddController extends Mage_ProductAlert_AddController
{
    public $anonymousId = null;
    
    public function preDispatch()
    {
        Mage_Core_Controller_Front_Action::preDispatch();

        if (Mage::helper('alertanonymous/login')->isLoggedIn()) {
            return;
        }

        $websiteId = Mage::app()->getWebsite()->getId();
        $store = Mage::app()->getStore();

        $customer = Mage::getModel("customer/customer");
        $customer   ->setWebsiteId($websiteId)
            ->setFirstname('_Anonymous_')
            ->setLastname('_Anonymous_')
            ->setEmail($this->getRequest()->getParam('email'))
            ->setPassword('Mage-' . substr(time(), 4));

        try{
            $customer->save();
        }
        catch (Exception $e) {
            Zend_Debug::dump($e->getMessage());
        }

        $this->anonymousId = $customer->getId();
    }

    private function manageSession()
    {
        if ($this->anonymousId === null) {
            return;
        }

        static $tumbler = 0;

        if ($tumbler === 0) {
            Mage::getSingleton('customer/session')->setId($this->anonymousId);
        } else {
            Mage::getSingleton('customer/session')->unsetData('id');
        }
        $tumbler = $tumbler + pow(-1, $tumbler);
    }

    public function priceAction()
    {
        $this->manageSession();

        parent::priceAction();

        $this->manageSession();
    }
}