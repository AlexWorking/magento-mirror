<?php

class Potoky_ItemBanner_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getAllStoreIds()
    {
        $storeIds = Mage::getStoreConfig('cms/itembanner/all_store_ids');
        if(!$storeIds) {
            $storeIds = 0;
        }
        if($storeIds == 0) {
            foreach (Mage::app()->getStores() as $store) {
                $storeIds .= $storeIds . ',' . $store->getId();
            }
            Mage::getModel('core/config')->saveConfig('cms/itembanner/all_store_ids', $storeIds);
        }

        return $storeIds;
    }

    public function getPositioningArray()
    {
        $positioningArray = unserialize(Mage::getStoreConfig('cms/itembanner/active_banners_positioning'));
        if(!$positioningArray) {
            $positioningArray = [];
            $storeIds = explode(',', $this->getAllStoreIds());
            foreach ($storeIds as $storeId) {
                $positioningArray[$storeId] = [];
            }
            Mage::getModel('core/config')->saveConfig('cms/itembanner/active_banners_positioning', $positioningArray);
        }

        return $positioningArray;
    }

    public function getWidgetRelatedData($widgetInstance, $ifActiveOnly = true)
    {
        $parameters = $widgetInstance->getWidgetParameters();
        $isActive = $parameters['is_active'];

        if (!$isActive && $ifActiveOnly) {
            return false;
        }

        return [$isActive => [
            'currentInstanceId' => $widgetInstance->getId(),
            'sortOrder'         => $widgetInstance->getData('sort_order'),
            'storeIds'          => ($parameters['store_ids'] == 0) ? explode(',', $this->getAllStoreIds()) : $parameters['store_ids'],
            'gridPosition'      => $parameters['position_in_grid'],
            'listPosition'      => $parameters['position_in_list'],
            'positioningArray'  => $this->getPositioningArray()
            ]
        ];
    }
}