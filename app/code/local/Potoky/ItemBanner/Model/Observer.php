<?php

class Potoky_ItemBanner_Model_Observer
{
    /**
     * To be written
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function addSearchHandles(Varien_Event_Observer $observer)
    {
        /* @var $widgetInstance Mage_Widget_Model_Widget_Instance */
        $widgetInstance = $observer->getEvent()->getObject();

        if($widgetInstance->getType() != "itembanner/banner") {

            return $this;
        }

        $pageGroups = $widgetInstance->getData('page_groups');
        foreach ($pageGroups as &$pageGroup) {
            if (in_array('catalog_category_layered', $pageGroup['layout_handle_updates']) ||
                in_array('catalog_category_default', $pageGroup['layout_handle_updates'])) {
                $pageGroup['layout_handle_updates'][] = 'catalogsearch_result_index';
                $pageGroup['layout_handle_updates'][] = 'catalogsearch_advanced_result';
            }
        }
        unset($pageGroup);
        $widgetInstance->setData('page_groups', $pageGroups);

        return $this;
    }

    /**
     * To be written
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function updateServiceData($observer)
    {
        $event = $observer->getEvent();
        switch ($event->getName()) {
            case 'store_add':
                $this->considerStoreAdd($event->getStore()->getId());
                break;
            case 'store_delete':
                $this->considerStoreDelete($event->getStore()->getId());
                break;
            case 'widget_widget_instance_save_after':
                $this->considerBannerSave($event->getObject());
                break;
            case 'widget_widget_instance_delete_after':
                $this->considerBannerDelete($event->getObject());
                break;
        }

        return $this;
    }

    /**
     * To be written
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function prepareDisplayBanners($observer)
    {
        $positioningArray = (unserialize(Mage::getStoreConfig('cms/itembanner/active_banners_positioning')));
        $layout = $observer->getLayout();
        $positioningArray = unserialize(Mage::getStoreConfig('cms/itembanner/active_banners_positioning'));
        if(!$positioningArray) {

            return $this;
        }

        $posInstArray = [];
        $storeId = Mage::app()->getStore()->getId();
        $toolbar = $layout->getBlock('product_list_toolbar');
        if(!$toolbar) {
            return $this;
        }
        $mode = $toolbar->getCurrentMode();
        foreach($positioningArray[$storeId][$mode] as $position => $orders) {
            $firstOrder = current($orders);
            $firstInstanceId = current($firstOrder);
            $posInstArray[$position] = $firstInstanceId;
        }
        
        return $this;
    }

    private function registerInfoToDb($widgetInstance)
    {
        $itemBannerInfo = Mage::getModel('itembanner/bannerinfo');
        if ($widgetInstance->isObjectNew()) {
            $itemBannerInfo ->setData([
                'instance_id' => $widgetInstance->getId(),
                'is_active'   => $widgetInstance->getWidgetParameters()['is_active']
            ]);
        } else {
            $itemBannerInfo->load($widgetInstance->getId(), 'instance_id');
            $itemBannerInfo->setData(
                'is_active',
                $widgetInstance->getWidgetParameters()['is_active']
            );
        }
        $itemBannerInfo->save();
    }

    private function considerStoreAdd($storeId)
    {
        $storeIds = Mage::getStoreConfig('cms/itembanner/all_store_ids');
        $storeIds = sprintf('%s,%s', $storeIds, $storeId);

        Mage::getModel('core/config')->saveConfig('cms/itembanner/all_store_ids', $storeIds);

        $positioningArray = Mage::helper('itembanner')->getPositioningArray();
        $positioningArray[$storeId] = $positioningArray[0];

        Mage::getModel('core/config')->saveConfig(
            'cms/itembanner/active_banners_positioning',
            serialize($positioningArray)
        );
    }

    private function considerStoreDelete($storeId)
    {
        $storeIds = Mage::getStoreConfig('cms/itembanner/all_store_ids');
        str_replace(',' . $storeId, '', $storeIds);

        Mage::getModel('core/config')->saveConfig('cms/itembanner/all_store_ids', $storeIds);

        $positioningArray = Mage::helper('itembanner')->getPositioningArray();
        unset($positioningArray[$storeId]);

        Mage::getModel('core/config')->saveConfig(
            'cms/itembanner/active_banners_positioning',
            serialize($positioningArray)
        );
    }

    private function considerBannerSave($widgetInstance)
    {
        $data = Mage::helper('itembanner')->getWidgetRelatedData($widgetInstance);

        if (!key($data)) {
            $positioningArray = $this->unsetPositioningArrayElements(current($data));
        } else {
            $positioningArray = $this->setPositioningArrayElements(current($data));
        }

        Mage::getModel('core/config')->saveConfig(
            'cms/itembanner/active_banners_positioning',
            serialize($positioningArray)
        );

        $this->registerInfoToDb($widgetInstance);
    }

    private function considerBannerDelete($widgetInstance)
    {
        $data = Mage::helper('itembanner')->getWidgetRelatedData($widgetInstance, true);
        if(!$data) {
            return;
        }

        $positioningArray = $this->unsetPositioningArrayElements($data['is_active']);

        Mage::getModel('core/config')->saveConfig(
            'cms/itembanner/active_banners_positioning',
            serialize($positioningArray)
        );
    }

    private function setPositioningArrayElements($data)
    {
        extract($data);
        foreach ($storeIds as $storeId) {
            if(!$positioningArray[$storeId]['grid'][$gridPosition][$sortOrder]) {
                $positioningArray[$storeId]['grid'][$gridPosition][$sortOrder][] = $currentInstanceId;
            }
            if (!$positioningArray[$storeId]['list'][$listPosition][$sortOrder]) {
                $positioningArray[$storeId]['list'][$listPosition][$sortOrder][] = $currentInstanceId;
            }
            rsort($positioningArray[$storeId]['grid'][$gridPosition]);
            rsort($positioningArray[$storeId]['list'][$listPosition]);
        }

        return $positioningArray;
    }

    private function unsetPositioningArrayElements($data)
    {
        extract($data);
        foreach ($storeIds as $storeId) {
            $gridKeyToUnset = array_search($currentInstanceId ,$positioningArray[$storeId]['grid'][$gridPosition][$sortOrder]);
            $listKeyToUnset = array_search($currentInstanceId ,$positioningArray[$storeId]['list'][$listPosition][$sortOrder]);
            unset($positioningArray[$storeId]['grid'][$gridPosition][$sortOrder][$gridKeyToUnset]);
            unset($positioningArray[$storeId]['list'][$listPosition][$sortOrder][$listKeyToUnset]);
        }

        return $positioningArray;
    }
}