<?php

class Potoky_ViewedProducts_Helper_Session extends Mage_Core_Helper_Abstract
{
    /**
     * Creates a cookie that should make the JS Block
     * clear its data or reload it from the server and
     * ads template with script to process it.
     *
     * @param string $type
     * @return void
     */
    public function processCookieForViewedProducts($type)
    {
        Mage::getModel('core/cookie')->set('viewed_products', $type, 0, '/', null, null, false);

        $layout = Mage::app()->getLayout();
        $endBlock = $layout->createBlock(
            'Mage_Core_Block_Template',
            'localstorage_rendering',
            array('template' => 'viewedproducts/process_cookie.phtml',
            ));
        $layout->getBlock('before_body_end')->append($endBlock);
    }
}

