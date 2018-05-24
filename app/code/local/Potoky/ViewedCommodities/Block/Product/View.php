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
        if (!Mage::helper('viewedcommodities')->isAllowedJsBlock()) {
            $this->shiftGatherer();
        } else {
            Mage::register('vieved_commodity', $this->getProdInfo());
        }

        return parent::_toHtml();
    }

    private function getProdInfo()
    {
        $prodsInfoArr = Mage::helper('viewedcommodities')
            ->getProductInfo([Mage::registry('current_product')]);
        $sku = array_keys($prodsInfoArr)[0];

        return [
            'sku'         => $sku,
            'productInfo' => Mage::helper('core')->jsonEncode($prodsInfoArr[$sku])
        ];
    }

    private function shiftGatherer()
    {
        $blockToAlter = $this->getLayout()->getBlock('product_data_gatherer');
        $blockToAlter->setTemplate('storage_execution');
    }
}