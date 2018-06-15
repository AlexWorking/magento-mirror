<?php

class Potoky_ViewedProducts_Helper_Lifetime extends Mage_Core_Helper_Abstract
{
    /**
     * Retrieves the lifetime of the JS Block.
     *
     * @return int $lifetime | null
     */
    public function getLifetime()
    {
        if (Mage::getStoreConfig('catalog/js_viewed_products/allow_jsblock')) {
            $lifetime = Mage::getStoreConfig('catalog/js_viewed_products/viewedproducts_lifetime');
            return $lifetime;
        }

        return null;
    }
}
