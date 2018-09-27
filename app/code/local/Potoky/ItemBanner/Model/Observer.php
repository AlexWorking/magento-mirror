<?php

class Potoky_ItemBanner_Model_Observer
{
    /**
     * To be written
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function additionalBeforeSave(Varien_Event_Observer $observer)
    {
        /* @var $widgetInstance Mage_Widget_Model_Widget_Instance */
        $widgetInstance = $observer->getEvent()->getObject();

        if($widgetInstance->getType() != "itembanner/banner") {

            return $this;
        }

        $this->addSearchHandles($widgetInstance);

        return $this;
    }

    /**
     * To be written
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function additionalAfterSave($observer)
    {
        /* @var $widgetInstance Mage_Widget_Model_Widget_Instance */
        $widgetInstance = $observer->getEvent()->getObject();

        if($widgetInstance->getType() != "itembanner/banner") {
            return $this;
        }

        $this->managePositioningArray($widgetInstance);
        $this->registerInfoToDb($widgetInstance);

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
        $eventName = $event->getName();
        switch ($eventName) {
            case 'store_add':
                $this->considerStoreAdd($event->getStore()->getId());
                break;
            case 'store_delete':
                $this->considerStoredelete($event->getStore()->getId());
                break;
            case 'widget_widget_instance_delete_after':
                $this->considerBannerDelete($event->getObject());
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
        $layout = $observer->getLayout();
        $positioningArray = unserialize(Mage::getStoreConfig('cms/itembanner/active_banners_positioning'));
        if(!$positioningArray) {

            return $this;
        }

        $posInstArray = [];
        $storeId = Mage::app()->getStore()->getId();
        $toolbar = $layout->getBlock('product_list_toolbar');
        $mode = $toolbar->getCurrentMode();
        foreach($positioningArray[$storeId][$mode] as $position => $orders) {
            $firstOrder = array_shift($orders);
            $firstInstanceId = array_shift($firstOrder);
            $posInstArray[$position] = $firstInstanceId;
        }
        
        return $this;
    }

    private function addSearchHandles($widgetInstance)
    {
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
    }

    private function managePositioningArray($widgetInstance)
    {
        $currentInstanceId = $widgetInstance->getId();
        $sortOrder = $widgetInstance->getData('sort_order');
        $storeIds = explode(
            ',',
            ($widgetInstance->getData('store_ids') != 0) ? $widgetInstance->getData('store_ids') : Mage::getStoreConfig('cms/itembanner/all_store_ids')
        );
        $parameters = $widgetInstance->getWidgetParameters();
        $gridPosition = $parameters['position_in_grid'];
        $listPosition = $parameters['position_in_list'];
        $positioningArray = unserialize(Mage::getStoreConfig('cms/itembanner/active_banners_positioning'));
        $positioningArray = ($positioningArray) ? $positioningArray : [];

        foreach ($storeIds as $storeId) {
            if (!$parameters['is_active']) {
                $gridKeyToUnset = array_search($currentInstanceId ,$positioningArray[$storeId]['grid'][$gridPosition][$sortOrder]);
                $listKeyToUnset = array_search($currentInstanceId ,$positioningArray[$storeId]['list'][$listPosition][$sortOrder]);
                unset($positioningArray[$storeId]['grid'][$gridPosition][$sortOrder][$gridKeyToUnset]);
                unset($positioningArray[$storeId]['list'][$listPosition][$sortOrder][$listKeyToUnset]);
            }
            $positioningArray[$storeId]['grid'][$gridPosition][$sortOrder][] = $currentInstanceId;
            $positioningArray[$storeId]['list'][$listPosition][$sortOrder][] = $currentInstanceId;
            rsort($positioningArray[$storeId]['grid'][$gridPosition]);
            rsort($positioningArray[$storeId]['list'][$listPosition]);
        }

        Mage::getModel('core/config')->saveConfig(
            'cms/itembanner/active_banners_positioning',
            serialize($positioningArray)
        );
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
        $storeIds = (!$storeIds) ? $storeId : sprintf('%s,%s', $storeIds, $storeId);

        Mage::getModel('core/config')->saveConfig('cms/itembanner/all_store_ids', $storeIds);
    }

    private function considerStoredelete($storeId)
    {
        $storeIds = Mage::getStoreConfig('cms/itembanner/all_store_ids');
        str_replace($storeId, '', $storeIds);
        str_replace(',,', ',', $storeIds);

        Mage::getModel('core/config')->saveConfig('cms/itembanner/all_store_ids', $storeIds);
    }

    private function considerBannerDelete($widgetInstance)
    {

    }
}