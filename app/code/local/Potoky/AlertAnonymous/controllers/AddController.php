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

        $email = $this->getRequest()->getParam('email');
        $websiteId = Mage::app()->getWebsite()->getId();

        $anonymousСustomer = Mage::getModel("anonymouscustomer/anonymous")
            ->getCollection()
            ->addFieldToFilter('email', $email)
            ->addFieldToFilter('website_id', $websiteId)
            ->addFieldToFilter('registration_id', array('null' => true))
            ->getFirstItem();
        if (!$anonymousСustomer->getId()) {
            $anonymousСustomer->setWebsiteId($websiteId)
                ->setEmail($email)
                ->setGroupId(1)
                ->setStoreId(Mage::app()->getStore()->getId());
            try {
                $anonymousСustomer->save();
            } catch (Exception $e) {
                Zend_Debug::dump($e->getMessage());
            }
        }
        Mage::unregister('potoky_alertanonymous');
        Mage::register('potoky_alertanonymous', ['last_anonymous_id' => $anonymousСustomer->getId()]);
    }
}