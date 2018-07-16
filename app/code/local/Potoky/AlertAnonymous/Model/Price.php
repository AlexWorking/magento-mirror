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
        $registry = (Mage::registry('potoky_alertanonymous')) ? Mage::registry('potoky_alertanonymous') : null;

        if ($registry === null || $registry['parent_construct'] === true) {
            parent::_construct();
        } else {
            $this->_init('alertanonymous/price');
        }
    }

    /**
     * @param int|null $value
     * @return $this
     */
    public function setCustomerId(int $value = null)
    {
        if($value == null) {
            $value =  Mage::registry('potoky_alertanonymous')['id'];
        }

        $this->setData(['customer_id' => $value]);
        return $this;
    }

    public function deleteCustomer($customerId = null, $websiteId = 0)
    {
        if (!$customerId) {
            $customerId = Mage::registry('potoky_alertanonymous')['id'];
        }

        return $this->getResource()->deleteCustomer($this, $customerId, $websiteId);
    }
}