<?php

class Potoky_ItemBanner_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function toBeDeactivatedIds(array $infoArray)
    {
        $activeForGridId = Mage::getModel('itembanner/bannerinfo')
            ->getCollection()
            ->addFieldToFilter('position_in_grid', $infoArray['grid']['position'])
            ->addFieldToFilter('active_for_grid', $infoArray['grid']['activeness'])
            ->getFirstItem()
            ->getData('instance_id');

        $activeForListId = Mage::getModel('itembanner/bannerinfo')
            ->getCollection()
            ->addFieldToFilter('position_in_list', $infoArray['list']['position'])
            ->addFieldToFilter('active_for_list', $infoArray['list']['activeness'])
            ->getFirstItem()
            ->getData('instance_id');


        return ['grid' => $activeForGridId, 'list' => $activeForListId];
    }

    public function getNamesOfActiveBlock()
    {
        return $this->toBeDeactivated()->getData('names_in_layout');
    }
}