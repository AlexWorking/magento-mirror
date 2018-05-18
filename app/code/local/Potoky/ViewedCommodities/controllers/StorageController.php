<?php

/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 5/18/2018
 * Time: 10:36 AM
 */
class Potoky_ViewedCommodities_StorageController extends Mage_Core_Controller_Front_Action
{
    public function getherAction()
    {
        $products = Mage::getModel('reports/product_index_viewed')
            ->getCollection()
            ->addAttributeToSelect(['name', 'thumbnail', 'url_key'])
            ->addIndexFilter();
        $prodsInfoArr = [];
        foreach ($products as $product) {
            $prodsInfoArr[] = Mage::helper('viewedcommodities')->getProductInfo($product);
        }

        echo Mage::helper('core')->jsonEncode($prodsInfoArr);;
    }
}