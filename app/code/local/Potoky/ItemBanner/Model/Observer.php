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
        $widgetInstance = $observer->getEvent()->getObject();

        $itemBannerInfo = Mage::getModel('itembanner/bannerinfo');
        if ($widgetInstance->isObjectNew()) {
            $itemBannerInfo ->setData('instance_id', $widgetInstance->getId());
            $itemBannerInfo->save();
        } else {
            $correspondingRecord = $itemBannerInfo->load($widgetInstance->getId(), 'instance_id');
            if ($correspondingRecord->getId() && !$correspondingRecord->getIsActive()) {
                $correspondingRecord->setData('is_active', true);
                $correspondingRecord->save();
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
        $itemBannerInfo = Mage::getModel('itembanner/bannerinfo')->load(true, 'is_active');

        if ($infoId = $itemBannerInfo->getId()) {
            if ($widgetInstance->isObjectNew() || $widgetInstance->getId() != $itemBannerInfo->getInstanceId()) {
                $itemBannerInfo->setData('is_active', false);
                $itemBannerInfo->save();
            }
        }

        return $this;
    }
}