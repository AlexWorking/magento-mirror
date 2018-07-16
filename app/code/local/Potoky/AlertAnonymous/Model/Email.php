<?php

class Potoky_AlertAnonymous_Model_Email extends Mage_ProductAlert_Model_Email
{
    const XML_PATH_EMAIL_PRICE_TEMPLATE = 'catalog/productalert/email_price_template_anonymous';
    const XML_PATH_EMAIL_STOCK_TEMPLATE = 'catalog/productalert/email_stock_template_anonymous';

    /**
     * Retrieve price block
     *
     * @return Mage_ProductAlert_Block_Email_Price
     */
    protected function _getPriceBlock()
    {
        $parent = parent::_getPriceBlock();
        if (Mage::registry('potoky_alertanonymous')['id'] == 'anonymous_email') {
            $parent->setUnsubscribeHash(Mage::helper('core')->encrypt(
                $this->_customer->getEmail() . ' ' . $this->_customer->getWebsiteId()
            ));
        }

        return $parent;
    }
}