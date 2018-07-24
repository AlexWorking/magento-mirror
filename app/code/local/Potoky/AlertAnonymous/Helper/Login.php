<?php
/**
 * Created by PhpStorm.
 * User: light
 * Date: 7/7/2018
 * Time: 10:46 PM
 */

class Potoky_AlertAnonymous_Helper_Login extends Mage_Core_Helper_Abstract
{
    /**
     * Show whether the customer is logged in or not
     *
     * @var boolean
     */
    private static $loggedIn;

    /**
     * Show whether Current customer is logged in
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        if (!isset(self::$loggedIn)) {
            self::$loggedIn = Mage::getSingleton('customer/session')->isLoggedIn();
        }

        return self::$loggedIn;
    }
}