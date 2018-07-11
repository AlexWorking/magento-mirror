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
        Mage::unregister('potoky_alertanonymous');

        $customer = Mage::helper('anonymouscustomer/entity')
            ->getCustomerEntityByRequest('customer/customer', $email, $websiteId);
        if ($id = $customer->getId()) {
            Mage::register('potoky_alertanonymous',
                [
                    'id' => $id,
                    'parent_construct' => true
                ]
            );
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
            return;
        }

        $anonymousCustomer->setWebsiteId($websiteId)
            ->setEmail($email)
            ->setGroupId(1)
            ->setStoreId(Mage::app()->getStore()->getId());
        try {
            $anonymousCustomer->save();
        } catch (Exception $e) {
            Zend_Debug::dump($e->getMessage());
        }

        Mage::register('potoky_alertanonymous',
            [
                'id' => $anonymousCustomer->getId(),
                'parent_construct' => false
            ]
        );
    }
}