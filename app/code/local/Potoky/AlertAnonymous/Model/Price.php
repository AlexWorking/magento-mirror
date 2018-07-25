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
        if (empty(self::$helpers)) {
            Mage::helper('alertanonymous')->setUpHelpers($this);
        }

        if (self::$helpers['registry']->getRegistry('parent_construct') === false) {
            $this->_init('alertanonymous/price');
        } else {
            parent::_construct();
        }
    }

    /**
     * Sets customer Id. Rewrites parent method in order to make it
     * possible to pass in the argument. (Parent method do not accept arguments)
     *
     * @param int|null $value
     * @return $this
     */
    public function setCustomerId(int $value = null)
    {
        if($registryValue = self::$helpers['registry']->getRegistry('customer_entity')) {
            $value =  $registryValue->getId();
        }

        $this->setData(['customer_id' => $value]);
        return $this;
    }

    /**
     * Delete alerts of the customer matched by passed in params. Rewrites parent
     * method in order dispatch needed event
     *
     * @param null $customerId
     * @param int $websiteId
     * @return Mage_ProductAlert_Model_Resource_Abstract
     */
    public function deleteCustomer($customerId = null, $websiteId = 0)
    {
        if ($registryCustomerId = self::$helpers['registry']->getRegistry('customer_entity')) {
            $customerId = $registryCustomerId->getId();
        }
        Mage::dispatchEvent('price_all_alert_delete_before');

        return $this->getResource()->deleteCustomer($this, $customerId, $websiteId);
    }

    public function _setDataSaveAllowed($bool){
        $this->_dataSaveAllowed = $bool;
    }
}