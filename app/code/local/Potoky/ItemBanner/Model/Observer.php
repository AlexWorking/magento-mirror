<?php

class Potoky_ItemBanner_Model_Observer
{
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

        if($widgetInstance->getType() == "itembanner/banner") {
            $this->addSearchHandles($widgetInstance)->deactivate($widgetInstance);
        }

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
                'is_active'   => $widgetInstance->getWidgetParameters()['is_active'],
                'page_groups' => 'test'
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
    public function manageTemplateReset($observer)
    {
        $layout = $observer->getLayout();
        $itemBanners = Potoky_ItemBanner_Block_Banner::$allOfTheType;
        if(!empty($itemBanners)) {
            foreach ($itemBanners as $itemBanner) {
                /* @var $block Potoky_ItemBanner_Block_Banner */
                $block = $layout->getBlock($itemBanner);
                if (!$block->getParentBlock() instanceof Mage_Catalog_Block_Product_List ||
                    !$block->getData('is_active')) {
                    $block->setTemplate('');
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

    private function deactivate($widgetInstance)
    {
        static $skipDeactivate = false;

        if($skipDeactivate) {
            $skipDeactivate = false;
            return $this;
        }
        /* @var $itemBannerInfo Potoky_ItemBanner_Model_Bannerinfo */
        $itemBannerInfo = Mage::helper('itembanner')->getActiveInstanceInfo();

        if ($itemBannerInfo->getId()) {
            $activeInstanceId = $itemBannerInfo->getInstanceId();
            if ($widgetInstance->getWidgetParameters()['is_active'] &&
                $widgetInstance->getId() != $activeInstanceId) {
                $previousActiveInstance = Mage::getModel('widget/widget_instance')->load($activeInstanceId);
                $parameters = $previousActiveInstance->getWidgetParameters();
                $parameters['is_active'] = 0;
                $previousActiveInstance->setData('widget_parameters', $parameters);
                $previousActiveInstance->setData(
                    'page_groups',
                    unserialize($itemBannerInfo->getData('page_groups'))
                );
                $skipDeactivate = true;
                $previousActiveInstance->save();
            }
        }
    }
}