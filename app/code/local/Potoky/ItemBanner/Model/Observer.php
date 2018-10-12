<?php

class Potoky_ItemBanner_Model_Observer
{
    /**
     * To be written
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     * @throws Mage_Core_Exception
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
    public function registerInfoToDb(Varien_Event_Observer $observer)
    {
        $widgetInstance = $observer->getEvent()->getObject();

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
    public function prepareDisplayBanners($observer)
    {
        $layout = $observer->getLayout();
        if (!Potoky_ItemBanner_Block_Banner::$allOfTheType) {

            return $this;
        }

        $option = Mage::getStoreConfig('cms/itembanner/rendering_type');
        $dataForPhtml = false;
        switch ($option) {
            case 1:
                $dataForPhtml = $this->renderFirstOccupy($layout);
                break;
            case 2:
                $dataForPhtml = $this->renderMoveToNext($layout);
                break;
        }

        if($dataForPhtml) {
            Mage::unregister('potoky_itembanner');
            Mage::register('potoky_itembanner', $dataForPhtml);
        }

        return $this;
    }

    /**
     * To be written
     *
     * @param Mage_Core_Model_Layout $layout
     * @return array | boolean
     */
    private function renderFirstOccupy($layout)
    {
        $toolbar = $layout->getBlock('product_list_toolbar');
        if(!$toolbar) {
            return false;
        }

        $priorityArray = Mage::helper('itembanner')->getBannerPriorityArray();
        $positioningArray = [];
        $positionField = sprintf('position_in_%s', $toolbar->getCurrentMode());
        $firstNum = ($toolbar->getCurrentPage() - 1) * $toolbar->getLimit() + 1;
        $lastNum = $firstNum - 1 + $toolbar->getLimit();
        $previousPagesBannerQty = 0;
        $nextPageBannersQty = 0;
        foreach (Potoky_ItemBanner_Block_Banner::$allOfTheType as $blockName) {
            $block = $layout->getBlock($blockName);
            $position = $block->getData($positionField);

            if(!$block->getData('is_active')) {
                continue;
            }

            if ($position < $firstNum) {
                $previousPagesBannerQty++;
                continue;
            }

            if ($position > $lastNum) {
                $nextPageBannersQty++;
                continue;
            }

            if ($occupying = $positioningArray[$position]) {
                $occupying = $layout->getBlock($occupying)->getData('instance_id');
                $wishing = $block-> getData('instance_id');
                if($priorityArray[$occupying] < $priorityArray[$wishing]) {
                    continue;
                }
            }
            $positioningArray[$position] = $blockName;
        }
        $data = [
            'previousPagesBannerQty' => $previousPagesBannerQty,
            'positioningArray'      => $positioningArray,
            'nextPageBannersQty'    => $nextPageBannersQty
        ];

        return $data;
    }

    /**
     * To be written
     *
     * @param Mage_Core_Model_Layout $layout
     * @return array | boolean
     */
    private function renderMoveToNext($layout)
    {
        $toolbar = $layout->getBlock('product_list_toolbar');
        if(!$toolbar) {
            return false;
        }

        $priorityArray = Mage::helper('itembanner')->getBannerPriorityArray();
        $positioningArray = [];
        $positionField = sprintf('position_in_%s', $toolbar->getCurrentMode());
        $firstNum = ($toolbar->getCurrentPage() - 1) * $toolbar->getLimit() + 1;
        $lastNum = $firstNum - 1 + $toolbar->getLimit();
        $previousPagesBannerQty = 0;
        $nextPageBannersQty = 0;
        foreach (Potoky_ItemBanner_Block_Banner::$allOfTheType as $blockName) {
            $block = $layout->getBlock($blockName);
            $position = $block->getData($positionField);
            
            if(!$block->getData('is_active')) {
                continue;
            }

            if ($position < $firstNum) {
                $previousPagesBannerQty++;
                continue;
            }

            if ($position > $lastNum) {
                $nextPageBannersQty++;
                continue;
            }

            if ($occupying = $positioningArray[$position]) {
                $occupying = $layout->getBlock($occupying)->getData('instance_id');
                $wishing = $block-> getData('instance_id');
                if ($priorityArray[$occupying] < $priorityArray[$wishing]) {
                    if ($position + 1 <= $lastNum) {
                        $positioningArray[$position + 1] = $blockName;
                    }

                    continue;
                } else {
                    if ($position + 1 <= $lastNum) {
                        $positioningArray[$position + 1] = $positioningArray[$position];
                    }
                }
            }
            $positioningArray[$position] = $blockName;
        }
        $data = [
            'previousPagesBannerQty' => $previousPagesBannerQty,
            'positioningArray'      => $positioningArray,
            'nextPageBannersQty'    => $nextPageBannersQty
        ];

        return $data;
    }
}