<?php

class Potoky_ViewedProducts_StorageController extends Mage_Core_Controller_Front_Action
{
    /**
     * Gatheres information about the Viewed Products on the server and
     * pass it to JS scripts via AJAX
     *
     * @return void
     */
    public function gatherAction()
    {
        $products = Mage::getModel('reports/product_index_viewed')
            ->getCollection()
            ->addAttributeToSelect(['name', 'thumbnail', 'url_key'])
            ->addIndexFilter();
        $prodsInfoArr = Mage::helper('viewedproducts/product')->getProductInfo($products);
        $lifeTime = $_POST['lifetime'];
        $expiry = time() + $lifeTime;
        $response = ['products_info' => $prodsInfoArr, 'expiry' => $expiry * 1000];
        echo Mage::helper('core')->jsonEncode($response);
    }
}
