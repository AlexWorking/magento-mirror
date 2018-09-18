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

        return $this;
    }


    public function addSearchHandles($widgetInstance)
    {
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

        if (!$widgetInstance->getId()) {
            $itemBannerInfo = Mage::getModel('itembanner/bannerinfo')->getCollection()
                ->addFieldToFilter('is_active',array('eq'=>true))
                ->selectFirstItem();
            if ($itemBannerInfo->getId()) {
                $itemBannerInfo->setData('is_active', false);
                $itemBannerInfo->save();
            }
        }

        return $this;
    }

    /**
     * To be written
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    private function disactivate($widgetInstance) {

    }
}