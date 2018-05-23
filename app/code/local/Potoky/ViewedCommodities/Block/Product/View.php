<?php

/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 5/23/2018
 * Time: 3:09 PM
 */
class Potoky_ViewedCommodities_Block_Product_View extends Mage_Catalog_Block_Product_View
{
    public function _toHtml()
    {
        if (isset($_SESSION['viewed_commodities'])) {
            if ($_SESSION['viewed_commodities'] === 'engaged') {
                return parent::_toHtml();
            }
            if (isset($_COOKIE['viewed_commodities'])) {
                $this->shiftGetherer();
            } else {
                $_SESSION['viewed_commodities'] = 'engaged';
            }
        } else {
            $this->shiftGetherer();
        }

        return parent::_toHtml();
    }

    private function getProdInfo()
    {
        $prodsInfoArr = Mage::helper('viewedcommodities')
            ->getProductInfo([Mage::registry('current_product')]);

        return [
            'sku'         => array_keys($prodsInfoArr)[0],
            'productInfo' => Mage::helper('core')->jsonEncode($prodsInfoArr[$sku])
        ];
    }

    private function shiftGetherer()
    {
        $blockToAlter = $this->getLayout()->getBlock('product_data_gatherer');
        $blockToAlter->setTemplate('storage_execution');
    }
}