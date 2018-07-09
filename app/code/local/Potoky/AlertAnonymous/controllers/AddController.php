<?php

require_once(
    Mage::getModuleDir('controllers','Mage_ProductAlert').
    DS.'AddController.php');
class Potoky_AlertAnonymous_AddController extends Mage_ProductAlert_AddController
{
    public function preDispatch()
    {
        Mage_Core_Controller_Front_Action::preDispatch();

        if (Mage::helper('alertanonymous/login')->isLoggedIn()) {
            return;
        }

        Mage::unregister('potoky_alertanonymous');
        Mage::register('potoky_alertanonymous', 'anonymouscustomer_create');

        $websiteId = Mage::app()->getWebsite()->getId();
        $storeId = Mage::app()->getStore()->getId();

        $anonymousСustomer = Mage::getModel("anonymouscustomer/anonymous");
        $anonymousСustomer   ->setWebsiteId($websiteId)
            ->setEmail($this->getRequest()->getParam('email'))
            ->setGroupId(1)
            ->setStoreId($storeId);

        try{
            $anonymousСustomer->save();
        }
        catch (Exception $e) {
            Zend_Debug::dump($e->getMessage());
        }

        Potoky_AlertAnonymous_Model_Price::$lastId = $anonymousСustomer->getId();

    }
}