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
        //unset($_SESSION['viewed_commodities']);
        //exit();
        if (isset($_SESSION['viewed_commodities'])) {
            if ($_SESSION['viewed_commodities'] === 'engaged') {
                return $this->loadFromJs();
            }
            if (isset($_COOKIE['viewed_commodities'])) {
                Mage::helper('viewedcommodities')->addJsVC(
                    $this->getLayout(),
                    'engage'
                );
            } else {
                $_SESSION['viewed_commodities'] = 'engaged';
                return $this->loadFromJs();
            }
        } else {
            $_SESSION['viewed_commodities'] = 'engage';
            Mage::helper('viewedcommodities')->addJsVC(
                $this->getLayout(),
                $_SESSION['viewed_commodities']
            );
        }

        return parent::_toHtml();
    }

    private function loadFromJs()
    {
        $this->setTemplate('viewedcommodities/commodity_viewed.phtml');
        $html = $this->renderView();
        return $html;
    }
}