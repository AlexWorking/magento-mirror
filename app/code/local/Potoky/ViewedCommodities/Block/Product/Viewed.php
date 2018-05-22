<?php

/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 5/21/2018
 * Time: 11:49 AM
 */
class Potoky_ViewedCommodities_Block_Product_Viewed extends Mage_Reports_Block_Product_Viewed
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
            $this->setTemplate('viewedcommodities/commodity_viewed.phtml');
            $html = $this->renderView();
            return $html;
        }

        return parent::_toHtml();
    }
}