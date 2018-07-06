<?php

require_once(
    Mage::getModuleDir('controllers','Mage_ProductAlert').
    DS.'AddController.php');
class Potoky_AlertAnonymous_AddController extends Mage_ProductAlert_AddController
{
    /**
     * Temporary customer
     *
     * @var Mage_Customer_Model_Customer
     */
    public $tempCustomer;

    public function preDispatch()
    {
        Mage_Core_Controller_Front_Action::preDispatch();

        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            return;
        }

        $websiteId = Mage::app()->getWebsite()->getId();
        $store = Mage::app()->getStore();

        $customer = Mage::getModel("customer/customer");
        $customer   ->setWebsiteId($websiteId)
            ->setStore($store)
            ->setFirstname('John1')
            ->setLastname('Doe1')
            ->setEmail($this->getRequest()->getParam('email'))
            ->setPassword('somepassword');

        try{
            $customer->save();
        }
        catch (Exception $e) {
            Zend_Debug::dump($e->getMessage());
        }

        $this->tempCustomer = $customer;
    }

    public function priceAction()
    {
        $session = Mage::getSingleton('catalog/session');
        $backUrl    = $this->getRequest()->getParam(Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED);
        $productId  = (int) $this->getRequest()->getParam('product_id');
        if (!$backUrl || !$productId) {
            $this->_redirect('/');
            return ;
        }

        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId()) {
            /* @var $product Mage_Catalog_Model_Product */
            $session->addError($this->__('Not enough parameters.'));
            if ($this->_isUrlInternal($backUrl)) {
                $this->_redirectUrl($backUrl);
            } else {
                $this->_redirect('/');
            }
            return ;
        }

        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            Mage::getSingleton('customer/session')->setId(130);
        }
        $customerId = (Mage::getSingleton('customer/session')->isLoggedIn()) ?
            Mage::getSingleton('customer/session')->getId() :
            $this->tempCustomer->getId();

        try {
            $model  = Mage::getModel('productalert/price')
                ->setCustomerId($customerId)
                ->setProductId($product->getId())
                ->setPrice($product->getFinalPrice())
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
            $model->save();
            $session->addSuccess($this->__('The alert subscription has been saved.'));
        }
        catch (Exception $e) {
            $session->addException($e, $this->__('Unable to update the alert subscription.'));
        }
        $this->_redirectReferer();
    }

}