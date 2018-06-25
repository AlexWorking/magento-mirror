<?php

class Potoky_AlertAnonymous_Block_Product_View extends Mage_ProductAlert_Block_Product_View
{
    /**
     * Check whether the price alert data can be shown and prepare related data
     *
     * @return void
     */
    public function preparePriceAlertData()
    {
        if (!$this->_getHelper()->isPriceAlertAllowed()
            || !$this->_product || false === $this->_product->getCanShowPrice()
        ) {
            $this->setTemplate('');
            return;
        }

        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->setTemplate('alertanonymous/product/view.phtml');
        }

        $this->setSignupUrl($this->_getHelper()->getSaveUrl('price'));
    }

}