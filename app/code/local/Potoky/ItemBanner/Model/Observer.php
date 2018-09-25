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
    public function removeInactiveItemBanners($observer)
    {
        $layout = $observer->getLayout();
        if(!empty(Potoky_ItemBanner_Block_Banner::$allOfTheType)) {
            foreach (Potoky_ItemBanner_Block_Banner::$allOfTheType as $itemBanner) {
                /* @var $block Potoky_ItemBanner_Block_Banner */
                $block = $layout->getBlock($itemBanner);
                if (!$block->getParentBlock() instanceof Mage_Catalog_Block_Product_List ||
                    !$block->getData('is_active')) {
                    $layout->unsetBlock($itemBanner);
                } else {
                    Potoky_ItemBanner_Block_Banner::$allOfTheType['active'] = $itemBanner;
                }
            }
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

    private function manageInstancDisplay($widgetInstance)
    {
        $parameters = $widgetInstance->getWidgetParameters();
        $id = $widgetInstance->getId();
        $displayArray = unserialize(Mage::getStoreConfig('cms/itembanner/displayed_banners_info'));
        $displayArray = ($displayArray) ? $displayArray : [
            'grid' => [
                'active' =>  [],
                'passive' => []
            ],
            'list' => [
                'active' =>  [],
                'passive' => []
            ]
        ];
        $instanceForCheckId = $displayArray['grid']['active'][$parameters['position_in_grid']];
        if ($instanceForCheckId && $instanceForCheckId != $id) {
            $instanceForCheck = Mage::getModel('widget/widget_instance')->load($instanceForCheckId);
            if ($this->getPriorInstance())
        }

        $displayArray['grid']['active']$parameters['position_in_grid'] = $id;
        $displayArray['list']['active']$parameters['position_in_list'] = $id;

        $existingInstances = Mage::getModel('widget/widget_instance')->getCollection();
        foreach ($existingInstances as $instance) {
            
        }
    }

}