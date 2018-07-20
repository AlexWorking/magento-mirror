<?php

class Potoky_AlertAnonymous_Model_Price extends Mage_ProductAlert_Model_Price
{
    public static $helpers = [];
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'price_alert';

    protected function _construct()
    {
        if (self::$helpers['registry']->getRegistry('parent_construct') === false) {
            $this->_init('alertanonymous/price');
        } else {
            parent::_construct();
        }
    }

    /**
     * @param int|null $value
     * @return $this
     */
    public function setCustomerId(int $value = null)
    {
        if($registryValue = self::$helpers['registry']->getRegistry('customer_entity')->getId()) {
            $value =  $registryValue;
        }

        $this->setData(['customer_id' => $value]);
        return $this;
    }

    public function deleteCustomer($customerId = null, $websiteId = 0)
    {
        if ($registryCustomerId = self::$helpers['registry']->getRegistry('customer_entity')->getId()) {
            $customerId = $registryCustomerId;
        }
        Mage::dispatchEvent('price_all_alert_delete_before');

        return $this->getResource()->deleteCustomer($this, $customerId, $websiteId);
    }

    protected function _beforeSave()
    {
        $anonymousCustomer = Mage::getModel('anonymouscustomer/anonymous')->load($this->getCusomerId());
        if($anonymousCustomer && $anonymousCustomer->getRegistrationId()) {
            $this->_dataSaveAllowed = false;
        }

        return parent::_beforeSave();
    }
}