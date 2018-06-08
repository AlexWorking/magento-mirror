<?php

class Potoky_ViewedProducts_Helper_Session extends Mage_Core_Helper_Abstract
{
    /**
     * Unsets session variable that stores the time of expiration of the JS Block
     * and  if parameter is passed in creates a cookie that should make the JS Block
     * clear its data or reload it from the server
     *
     * @param string $type
     * @return void
     */
    public function unsetSessionSetCookieForViewedProducts($type = null)
    {
        if (isset($_SESSION['viewed_products'])) {
            unset($_SESSION['viewed_products']);
        }
        if ($type !== null) {
            setcookie('viewed_products', $type, 0, '/');
        }
    }
}
