<?php

class Potoky_AlertAnonymous_Block_Product_View extends Mage_ProductAlert_Block_Product_View
{
    /**
     * Shows whether it is  a logged in session
     *
     * @var
     */
    static private $isLoggedIn;

    /**
     * The id (if set) of the self template highest level DOM element
     *
     * @var
     */
    private $templateId = null;

    /**
     * Sets $isLoggedIn property
     *
     * @return void
     */
    private static function setIsLoggedIn() {
        self::$isLoggedIn = Mage::getModel('customer/session')->isLoggedIn();
    }

    /**
     * Sets $isLoggedIn property
     *
     * @return $isLoggedIn property
     */
    public static function getIsLoggedIn() {
        if (!isset(self::$isLoggedIn)) {
            self::setIsLoggedIn();
        }

        return self::$isLoggedIn;
    }

    /**
     * Sets Id to the self template
     *
     * @return string
     */
    public function getTemplateId()
    {
        return $this->templateId;
    }

    /**
     * Check whether the stock alert data can be shown and prepare related data
     *
     * @return void
     */
    public function prepareStockAlertData()
    {
        $this->templateId = 'stock';

        parent::preparePriceAlertData();
    }

    /**
     * Check whether the price alert data can be shown and prepare related data
     *
     * @return void
     */
    public function preparePriceAlertData()
    {
        $this->templateId = 'price';

        parent::preparePriceAlertData();
    }
}