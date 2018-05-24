<?php

/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 5/18/2018
 * Time: 10:36 AM
 */
class Potoky_ViewedCommodities_StorageController extends Mage_Core_Controller_Front_Action
{
    public function gatherAction()
    {
        $products = Mage::getModel('reports/product_index_viewed')
            ->getCollection()
            ->addAttributeToSelect(['name', 'thumbnail', 'url_key'])
            ->addIndexFilter();
        $prodsInfoArr = Mage::helper('viewedcommodities')->getProductInfo($products);
        //$lifeTime = Mage::getStoreConfig('x/y');
        $expiry = microtime() + 3600000;
        $_SESSION['viewed_commodities'] = [round($expiry / 1000)];
        $response = ['products_info' => $prodsInfoArr, 'expiry' => $expiry];
        setcookie('viewed_commodities', 'engage', 0,'/');
        echo Mage::helper('core')->jsonEncode($response);;
    }
}