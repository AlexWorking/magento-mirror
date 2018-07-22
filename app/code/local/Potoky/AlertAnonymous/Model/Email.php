<?php

class Potoky_AlertAnonymous_Model_Email extends Mage_ProductAlert_Model_Email
{
    const XML_PATH_EMAIL_PRICE_TEMPLATE = 'catalog/productalert/email_price_template_anonymous';
    const XML_PATH_EMAIL_STOCK_TEMPLATE = 'catalog/productalert/email_stock_template_anonymous';

    public static $helpers = [];

    protected function _construct(){
        parent::_construct();
        if (empty(self::$helpers)) {
            Mage::helper('alertanonymous')->setUpHelpers($this);
        }
    }

    /**
     * Retrieve price block
     *
     * @return Mage_ProductAlert_Block_Email_Price
     */
    protected function _getPriceBlock()
    {
        $parent = parent::_getPriceBlock();
        $parent->setUnsubscribeHash(self::$helpers['data_1']->encrypt(
            $this->_customer->getEmail() . ' ' . $this->_customer->getWebsiteId()
            ));

        return $parent;
    }

    /**
     * Set customer by id
     *
     * @param int $customerId
     * @return Mage_ProductAlert_Model_Email
     */
    public function setCustomerId($customerId)
    {
        $customer = Mage::getModel('customer/customer')->load($customerId);
        if (!$customer->getRegistrationId()) {
            $this->_customer = $customer;
        } else {
            $this->_customer = null;
        }

        return $this;
    }

    /**
     * Set customer model
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return Mage_ProductAlert_Model_Email
     */
    public function setCustomer(Mage_Customer_Model_Customer $customer)
    {
        if (!$customer->getRegistrationId()) {
            $this->_customer = $customer;
        } else {
            $this->_customer = null;
        }

        return $this;
    }
}