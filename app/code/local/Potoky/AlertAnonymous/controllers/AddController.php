<?php

require_once(
    Mage::getModuleDir('controllers','Mage_ProductAlert').
    DS.'AddController.php');
class Potoky_AlertAnonymous_AddController extends Mage_ProductAlert_AddController
{
    private $email;
    
    private $websiteId;

    private $customerEntity;
    
    public function preDispatch()
    {
        Mage_Core_Controller_Front_Action::preDispatch();

        if (Mage::helper('alertanonymous/login')->isLoggedIn()) {
            return;
        }

        $this->email = $this->getRequest()->getParam('email');
        $this->websiteId = Mage::app()->getWebsite()->getId();

        if ($this->arrangeRegistry('customer/customer')) {
            return;
        }

        if ($this->arrangeRegistry('anonymouscustomer/anonymous')) {
            return;
        }

        $anonymousCustomer = Mage::getModel("anonymouscustomer/anonymous")
            ->setWebsiteId($this->websiteId)
            ->setEmail($this->email)
            ->setGroupId(1)
            ->setStoreId(Mage::app()->getStore()->getId());
        try {
            $anonymousCustomer->save();
            $this->customerEntity = $anonymousCustomer;
        } catch (Exception $e) {
            Zend_Debug::dump($e->getMessage());
        }

        $this->arrangeRegistry();
    }

    private function getCustomerEntityByRequest($modelType = null)
    {
        if ($modelType !== null) {
            $collection = Mage::getModel($modelType)
                ->getCollection()
                ->addFieldToFilter('email', $this->email)
                ->addFieldToFilter('website_id', $this->websiteId);
            if ($modelType === 'anonymouscustomer/anonymous')
            {
                $collection->addFieldToFilter('registration_id', array('null' => true));
            }
            $customerEntity = $collection->getFirstItem();
        } else {
            $customerEntity = $this->customerEntity;
        }

        return $customerEntity;
    }

    private function arrangeRegistry($modelType = null)
    {
        $customerEntity = $this->getCustomerEntityByRequest($modelType);
        if ($id = $customerEntity->getId()) {
            $idKey = substr($modelType, strpos($modelType, '/') + 1);
            Mage::unregister('potoky_alertanonymous');
            Mage::register('potoky_alertanonymous', [$idKey => $id]);
            return true;
        }

        return false;
    }
}