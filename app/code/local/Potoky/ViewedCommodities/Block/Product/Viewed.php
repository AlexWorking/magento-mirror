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
     * Internal constructor, that is called from real constructor
     *
     */
    protected function _construct()
    {
        Mage::register('viewed_block', 'engaged');

        parent::_construct();
    }

    /**
     * Prepare to html
     * check if JS block forming has started to perform.
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (Mage::registry('viewed_block') === 'allowed') {
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

    /*protected function _prepareLayout()
    {
        //unset($_SESSION['viewed_commodities']);
        $this->getLayout()->getBlock('head')->addJs('local/storage.js');
        if (!Mage::helper('viewedcommodities')->isAllowedJsBlock()) {
            Mage::helper('viewedcommodities')->addJsVC(
                $this->getLayout(),
                'storage_execution.phtml',
                'reset'
            );
        }

        return parent::_prepareLayout();
    }*/
}