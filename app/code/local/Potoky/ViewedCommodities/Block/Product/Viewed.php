<?php

/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 5/21/2018
 * Time: 11:49 AM
 */
class Potoky_ViewedCommodities_Block_Product_Viewed extends Mage_Reports_Block_Product_Abstract
{
    /**
     * Prepare to html
     * check if JS block forming has started to perform.
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (isset($_COOKIE['viewedcommodities'])) {
            $grandParent = new Mage_Core_Block_Template;
            $grandParent->setTemplate('viewedcommodities/commodity_viewed.phtml');
            return $grandParent->_toHtml();
        }

        return parent::_toHtml();
    }
}