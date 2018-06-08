<?php

class Potoky_ViewedProducts_Helper_Lifetime extends Mage_Core_Helper_Abstract
{
    /**
     * Lifetime of JS Block
     *
     * @var int
     */
    private $lifetime;

    /**
     * Retrieves the lifetime of the JS Block.
     *
     * @return int $this->lifetime
     */
    public function getLifetime()
    {
        if (!isset($this->lifetime) && Mage::getStoreConfig('catalog/lifetime_vc/allow_jsblock')) {
            $lifetime = Mage::getStoreConfig('catalog/lifetime_vc/viewedproducts_lifetime');
            $this->lifetime = $lifetime;
        }

        return $this->lifetime;
    }
}