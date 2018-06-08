<?php

class Potoky_ViewedProducts_Helper_Lifetime extends Mage_Core_Helper_Abstract
{
    /**
     * Lifetime of JS Block
     *
     * @var int
     */
    private static $lifetime;

    /**
     * Retrieves the lifetime of the JS Block.
     *
     * @return int self::$lifetime
     */
    public function getLifetime()
    {
        if (!isset(self::$lifetime) && Mage::getStoreConfig('catalog/lifetime_vc/allow_jsblock')) {
            $lifetime = Mage::getStoreConfig('catalog/lifetime_vc/viewedproducts_lifetime');
            self::$lifetime = $lifetime;
        }

        return self::$lifetime;
    }
}