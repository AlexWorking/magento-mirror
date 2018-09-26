<?php

class Potoky_ItemBanner_Model_Observer
{
    /**
     * To be written
     * 
     * @var 
     */
    private $saveWithoutController;

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

        $this->addSearchHandles($widgetInstance)->manageInstancDisplay($widgetInstance);

        return $this;
    }

    /**
     * To be written
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function registerInfoToDb($observer)
    {
        /* @var $widgetInstance Mage_Widget_Model_Widget_Instance */
        $widgetInstance = $observer->getEvent()->getObject();

        if($widgetInstance->getType() != "itembanner/banner") {
            return $this;
        }

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

        return $this;
    }

    /**
     * To be written
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function removeNoDisplayBanners($observer)
    {
        $layout = $observer->getLayout();
        $positioningArray = unserialize(Mage::getStoreConfig('cms/itembanner/banners_positioning'));
        if(!$positioningArray) {

            return $this;
        }

        $posInstArray = [];
        $storeId = Mage::app()->getStore()->getId();
        $toolbar = $layout->getBlock('product_list_toolbar');
        $mode = $toolbar->getCurrentMode();
        foreach($positioningArray[$storeId][$mode] as $position) {
            $firstOrder = array_shift($position);
            $posInstArray[]
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

        return $this;
    }

    private function managePositionArray($widgetInstance)
    {
        $currentInstanceId = $widgetInstance->getId();
        $sortOrder = $widgetInstance->getData('sort_order');
        $storeIds = explode(',', $widgetInstance->getData('store_ids'));
        $parameters = $widgetInstance->getWidgetParameters();
        $gridPosition = $parameters('position_in_grid');
        $listPosition = $parameters('position_in_list');
        $positioningArray = unserialize(Mage::getStoreConfig('cms/itembanner/banners_positioning'));
        $positioningArray = ($positioningArray) ? $positioningArray : [];

        foreach ($storeIds as $storeId) {
            if (!$parameters['is_active']) {
                continue;
            }
            $positioningArray[$storeId]['grid'][$gridPosition][$sortOrder][] = $currentInstanceId;
            $positioningArray[$storeId]['list'][$listPosition][$sortOrder][] = $currentInstanceId;
        }

        Mage::getModel('core/config')->saveConfig(
            'cms/itembanner/banners_positioning',
            serialize($positioningArray)
        );
    }

}