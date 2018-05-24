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
                'reset'
            );
            $this->allowed = false;
        } else {
            $this->getLayout()->getBlock('head')->addJs('local/storage.js');
        }

        return parent::_prepareLayout();
    }
}