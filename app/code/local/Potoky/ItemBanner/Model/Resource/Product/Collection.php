<?php

class Potoky_ItemBanner_Model_Resource_Product_Collection extends Mage_Catalog_Model_Resource_Product_Collection
{
    public function getSize()
    {
        $parent = parent::getSize();

        if($data = Mage::registry('potoky_itembanner')) {
            $parent += $data['previousPagesBannerQty'] + count($data['positioningArray']) + $data['nextPageBannersQty'];
        }

        return $parent;
    }

    public function setCurPage($page)
    {
        if ($data = Mage::registry('potoky_itembanner')) {
            $page = ceil((float) $page + $data['previousPagesBannerQty']);
        }

        return parent::setCurPage($page);
    }
}