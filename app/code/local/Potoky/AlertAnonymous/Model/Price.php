<?php

class Potoky_AlertAnonymous_Model_Price extends Mage_ProductAlert_Model_Price
{
    /*
     * The id of the last unregistered customer created for alert
     *
     * @var int
     */
    private $lastId;

    protected function _construct()
    {
        $registry = (Mage::registry('potoky_alertanonymous')) ? Mage::registry('potoky_alertanonymous') : null;
        if(null !== $registry) {
            $this->_init('alertanonymous/price');
            $this->lastId = isset($registry['last_anonymous_id']) ? $registry['last_anonymous_id'] : null;
        } else {
            parent::_construct();
        }
    }

    public function setCustomerId(int $value = null)
    {
        $value = (null !== $this->lastId) ? $this->lastId : $value;

        return parent::setCustomerId($value);
    }
}