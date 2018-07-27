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
        if ($customerId = $customer->getId()) {
            parent::preDispatch();
            $session = Mage::getSingleton('customer/session');
            $sessionId = $session->getId();
            if (!$sessionId || $customerId == $sessionId) {
                self::$helpers['registry']->setRegistry(null, $customer, true);
            } else {
                $session->setId(null);
                $session->addNotice(
                    'Please log in with the credentials of the Customer You\'ve been trying to unsubscribe and repeat the trial.'
                );
                $this->setFlag('', 'no-dispatch', true);
                $this->_redirect('customer/account/');
            }

            return;
        }

        $anonymousCustomer = self::$helpers['entity']->getCustomerEntityByRequest('anonymouscustomer/anonymous', $email, $websiteId);
        if ($id = $anonymousCustomer->getId()) {
            self::$helpers['registry']->setRegistry(null, $anonymousCustomer, false);
        }

        Mage_Core_Controller_Front_Action::preDispatch();
    }
}