<?php

class Potoky_ItemBanner_Helper_Select extends Mage_Core_Helper_Abstract
{
    public function getBannerBlockNames()
    {
        $itemBannerInfo = Mage::getModel('itembanner/bannerinfo')->load(true, 'is_active');

        if(!$instanceId = $itemBannerInfo->getInstanceId()) {
            return false;
        }

        $collection = Mage::getModel('itembanner/bannerinfo')->getCollection();
        $firstToJoin = Mage::getSingleton('core/resource')->getTableName('widget/widget_instance_page');
        $secondJoin = Mage::getSingleton('core/resource')->getTableName('widget/widget_instance_page_layout');
        $thirdToJoin = Mage::getSingleton('core/resource')->getTableName('core/layout_update');
        $collection->getSelect()
            ->joinLeft(
                array('ftj' => '$firstToJoin'),
                'banner_info.instance_id = ftj.instance_id',
                array()
            )
            ->joinLeft(
                array('stj' => '$secondJoin'),
                'ftj.page_id = stj.page_id',
                array()
            )
            ->joinLeft(
                array('ttj' => $thirdToJoin),
                'stj.layout_update_id = ttj.layout_update_id',
                array()
            );
        return $collection;
    }
}