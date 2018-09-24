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
    public function additionalBeforeSave($observer)
    {
        /* @var $widgetInstance Mage_Widget_Model_Widget_Instance */
        $widgetInstance = $observer->getEvent()->getObject();

        if($widgetInstance->getType() != "itembanner/banner") {

            return $this;
        }

        if ($this->saveWithoutController) {
            $origPageGroups = $widgetInstance->getOrigData('page_groups');
            $pageGroups = [];
            $pageGroupIds = [];
            foreach ($origPageGroups as $number => $origPageGroup) {
                $pageGroups[$number]['page_id'] = $origPageGroup['page_id'];
                $pageGroups[$number]['group'] = $origPageGroup['page_group'];
                $pageGroups[$number]['layout_handle'] = $origPageGroup['layout_handle'];
                $pageGroups[$number]['block_reference'] = $origPageGroup['block_reference'];
                $pageGroups[$number]['for'] = $origPageGroup['page_for'];
                $pageGroups[$number]['entities'] = $origPageGroup['entities'];
                $pageGroups[$number]['template'] = $origPageGroup['page_template'];
                $pageGroups[$number]['layout_handle_updates'][] = $origPageGroup['layout_handle'];

                $pageGroupIds[] = $origPageGroup['page_id'];
            }
            $widgetInstance->setData('page_groups', $pageGroups);
            $widgetInstance->setData('page_group_ids', $pageGroupIds);
        } else {
            $this->manageDeactivation($widgetInstance);
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

    private function manageDeactivation($widgetInstance)
    {
        $parameters = $widgetInstance->getWidgetParameters();
        if ($parameters['is_active']) {
            $active_for_grid = $parameters['active_for_grid'];
            $active_for_list = $parameters['active_for_list'];
        }
        /* @var $itemBannerInfo Potoky_ItemBanner_Model_Bannerinfo */
        $itemBannerInfo = Mage::helper('itembanner')->toBeDeactivated();

        if ($itemBannerInfo->getId()) {
            $activeInstanceId = $itemBannerInfo->getInstanceId();
            if ($widgetInstance->getWidgetParameters()['is_active'] &&
                $widgetInstance->getId() != $activeInstanceId) {
                $previousActiveInstance = Mage::getModel('widget/widget_instance')->load($activeInstanceId);
                $parameters = $previousActiveInstance->getWidgetParameters();
                $parameters['is_active'] = 0;
                $previousActiveInstance->setData('widget_parameters', $parameters);
                $this->saveWithoutController = true;
                $previousActiveInstance->save();
            }
        }

        return $this;
    }
}