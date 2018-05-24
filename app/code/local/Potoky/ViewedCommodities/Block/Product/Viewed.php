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
     * Defines whether where to load Viewed Products from: true => localstorage, false => server
     *
     *@var boolean
     */
    protected $allowed = true;

    /**
     * Prepare to html
     * check if JS block forming has started to perform.
     *
     * @return string
     */
    protected function _toHtml()
    {
        //echo "HERE".$_SESSION['viewed_commodities'];
        //unset($_SESSION['viewed_commodities']);
        //die($_SESSION['viewed_commodities']);
        if ($this->allowed) {
                return $this->loadFromJs();
        }

        return parent::_toHtml();
    }

    private function loadFromJs()
    {
        $this->setTemplate('viewedcommodities/commodity_viewed.phtml');
        $html = $this->renderView();
        return $html;
    }

    protected function _prepareLayout()
    {
        if (!Mage::helper('viewedcommodities')->isAllowedJsBlock()) {
            Mage::helper('viewedcommodities')->addJsVC(
                $this->getLayout(),
                'engage'
            );
            $this->allowed = false;
        }

        return parent::_prepareLayout();
    }
}