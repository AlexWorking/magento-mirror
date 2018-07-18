<?php

class Potoky_AlertAnonymous_Model_Price extends Mage_ProductAlert_Model_Price
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'price_alert';

    protected function _construct()
    {
        if (Mage::registry('potoky_alertanonymous')['parent_construct'] === false) {
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
        if($registryValue = Mage::registry('potoky_alertanonymous')['id']) {
            $value =  $registryValue;
        }

        $this->setData(['customer_id' => $value]);
        return $this;
    }

    public function deleteCustomer($customerId = null, $websiteId = 0)
    {
        if ($registryCustomerId = Mage::registry('potoky_alertanonymous')['id']) {
            $customerId = $registryCustomerId;
        }
        Mage::dispatchEvent('priceall_alert_delete_before');

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