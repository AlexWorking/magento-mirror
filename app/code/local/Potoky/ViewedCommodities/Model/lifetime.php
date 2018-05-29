<?php

class Potoky_ViewedCommodities_Model_Lifetime extends Mage_Core_Model_Config_Data
{
    /**
     * Validates data entered to the module"s System Configuration field
     * and saves the configuration in case of success.
     *
     * @return Mage_Core_Model_Abstract
     * @throws Mage_Core_Exception $e
     */
    public function save()
    {
        $lifetime = $this->getValue();
        $sessCookLifetime = (int) Mage::getStoreConfig(Mage_Core_Model_Cookie::XML_PATH_COOKIE_LIFETIME);
        if (!preg_match('#^[\s]*$#', $lifetime)) {
            if (filter_var($lifetime, FILTER_VALIDATE_INT, array(
                "options" => array(
                    "min_range" => 0,
                ))) === false) {

                Mage::throwException('The value of the field either should be empty or be equal zero or an integer.');
            } elseif ($lifetime > $sessCookLifetime) {
                Mage::getSingleton('adminhtml/session')
                    ->addNotice(
                        'Please mind that the  lifetime of the JS block will not last longer then the session cookie lifetime
                         which for now is configured to ' . $sessCookLifetime . ' sec. It is also strictly dependant on other sessional lifetimes.'
                    );
            }
        }
        return parent::save();
    }
}