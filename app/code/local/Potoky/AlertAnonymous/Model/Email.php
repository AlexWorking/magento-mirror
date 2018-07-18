<?php

class Potoky_AlertAnonymous_Model_Email extends Mage_ProductAlert_Model_Email
{
    const XML_PATH_EMAIL_PRICE_TEMPLATE = 'catalog/productalert/email_price_template_anonymous';
    const XML_PATH_EMAIL_STOCK_TEMPLATE = 'catalog/productalert/email_stock_template_anonymous';

    public static $noDuplicatePriceSend = [];

    /**
     * Retrieve price block
     *
     * @return Mage_ProductAlert_Block_Email_Price
     */
    protected function _getPriceBlock()
    {
        $parent = parent::_getPriceBlock();
        $parent->setUnsubscribeHash(Mage::helper('core')->encrypt(
            $this->_customer->getEmail() . ' ' . $this->_customer->getWebsiteId()
            ));

        return $parent;
    }

    /**
     * Add product (price change) to collection
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_ProductAlert_Model_Email
     */
    public function addPriceProduct(Mage_Catalog_Model_Product $product)
    {
        if(Mage::registry('potoky_alertanonymous')['parent_construct'] !== false) {
            self::$noDuplicatePriceSend[$this->_customer->getWebsiteId()][$this->_customer->getEmail()][$product->getId()] = true;
            $this->_priceProducts[$product->getId()] = $product;
        }
        elseif (!self::$noDuplicatePriceSend[$this->_customer->getWebsiteId()][$this->_customer->getEmail()][$product->getId()]) {
            $this->_priceProducts[$product->getId()] = $product;
        }

        return $this;
    }
}