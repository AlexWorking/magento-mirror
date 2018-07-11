<?php

class Potoky_AnonymousCustomer_Model_Observer
{
    public function moveToRegistered(Varien_Event_Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $anonymousCustomer = $this->findCorrespondingAnonymous($customer);
        if($id = $anonymousCustomer->getId()) {
            $anonymousCustomer
                ->setRegistrationId($customer->getId())
                ->setRegisteredAt($customer->getCreatedAt());
            try {
                $anonymousCustomer->save();
            } catch (Exception $e) {
                Zend_Debug::dump($e->getMessage());
            }
        }
    }

    public function onDeleteCascade(Varien_Event_Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $anonymousCustomer = $this->findCorrespondingAnonymous($customer);
        if($id = $anonymousCustomer->getId()) {
            try {
                $anonymousCustomer->delete();
            } catch (Exception $e) {
                Zend_Debug::dump($e->getMessage());
            }
        }
    }

    private function findCorrespondingAnonymous($customer)
    {
        $email = $customer->getEmail();
        $websiteId = $customer->getWebsiteId();
        $anonymousCustomer = Mage::helper('anonymouscustomer/anonymus')
            ->getCustomerEntityByRequest('customer/customer', $email, $websiteId);

        return $anonymousCustomer;
    }
}