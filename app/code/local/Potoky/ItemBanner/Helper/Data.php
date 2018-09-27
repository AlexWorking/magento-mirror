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

    public function getWidgetRelatedData($widgetInstance, $ifActiveOnly = true)
    {
        $parameters = $widgetInstance->getWidgetParameters();
        $isActive = $parameters['is_active'];

        if (!$isActive && $ifActiveOnly) {
            return false;
        }

        return [
            'currentInstanceId' => $widgetInstance->getId(),
            'sortOrder'         => $widgetInstance->getData('sort_order'),
            'storeIds'          => explode(
                ',',
                ($widgetInstance->getData('store_ids') != 0) ? $widgetInstance->getData('store_ids') : Mage::getStoreConfig('cms/itembanner/all_store_ids')
            ),
            'gridPosition'      => $parameters['position_in_grid'],
            'listPosition'      => $parameters['position_in_list'],
            'is_active'         => $isActive,
            'positioningArray'  => unserialize(Mage::getStoreConfig('cms/itembanner/active_banners_positioning'))
        ];
    }
}