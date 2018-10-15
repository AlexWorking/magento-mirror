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

    public function getCurPage($displacement = 0)
    {
        $parent = parent::getCurPage($displacement);
        static $float = true;
        if($float && $data = Mage::registry('potoky_itembanner')) {
            $backTrace = debug_backtrace(2, 2)[1];
            if ($backTrace['function'] == '_loadEntities' && $backTrace['class'] == 'Mage_Eav_Model_Entity_Collection_Abstract') {
                        $prevPageElementNumber = ($parent - 1) * $this->getPageSize();
                        $prevPageProdNumber = $prevPageElementNumber - $data['previousPagesBannerQty'];
                        $parent = (float) ($prevPageProdNumber / $this->getPageSize() + 1);
                        $float = false;
            }
        }

        return $parent;
    }
}