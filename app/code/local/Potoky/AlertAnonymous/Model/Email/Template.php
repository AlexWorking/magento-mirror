<?php

class Potoky_AlertAnonymous_Model_Email_Template extends Mage_Core_Model_Email_Template
{
    public static $helpers;

    public function _construct()
    {
        parent::_construct();
        if (empty(self::$helpers)) {
            Mage::helper('alertanonymous')->setUpHelpers($this);
        }
    }

    public function sendTransactional($templateId, $sender, $email, $name, $vars=array(), $storeId=null) {
        if (self::$helpers['registry']->getRegistry('context') === 'cron') {
            if ($templateId == Mage::getStoreConfig(Mage_ProductAlert_Model_Email::XML_PATH_EMAIL_PRICE_TEMPLATE, $storeId)) {
                $templateId = Mage::getStoreConfig(Potoky_AlertAnonymous_Model_Email::XML_PATH_EMAIL_PRICE_TEMPLATE, $storeId);
            }
            elseif ($templateId == Mage::getStoreConfig(Mage_ProductAlert_Model_Email::XML_PATH_EMAIL_STOCK_TEMPLATE, $storeId)) {
                $templateId = Mage::getStoreConfig(Potoky_AlertAnonymous_Model_Email::XML_PATH_EMAIL_STOCK_TEMPLATE, $storeId);
            }
        }

        return parent::sendTransactional($templateId, $sender, $email, $name, $vars, $storeId);
    }
}