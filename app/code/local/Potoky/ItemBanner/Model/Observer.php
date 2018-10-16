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
                $dataForPhtml = $this->renderPriorOccupy($layout);
                break;
            case 2:
                $dataForPhtml = $this->renderOccupyNext($layout);
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
    private function renderPriorOccupy($layout)
    {
        /* @var $toolbar Mage_Catalog_Block_Product_List_Toolbar */
        $toolbar = $layout->getBlock('product_list_toolbar');
        if(!$toolbar) {
            return false;
        }

        $priorityArray = Mage::helper('itembanner')->getBannerPriorityArray();
        $positioningArray = [];
        $positionField = sprintf('position_in_%s', $toolbar->getCurrentMode());
        $maxNum = 3 * Mage::getStoreConfig(sprintf('catalog/frontend/%s_per_page', $toolbar->getCurrentMode()));
        foreach (Potoky_ItemBanner_Block_Banner::$allOfTheType as $blockName) {
            $block = $layout->getBlock($blockName);
            $position = $block->getData($positionField);

            if (!$block->getData('is_active')) {
                continue;
            }

            if ($position > $maxNum) {
                continue;
            }

            if ($occupyingBlockName = $positioningArray[$position]) {
                $occupyingBlockId = $layout->getBlock($occupyingBlockName)->getData('instance_id');
                $wishingBlockId = $block-> getData('instance_id');
                if($priorityArray[$occupyingBlockId] < $priorityArray[$wishingBlockId]) {
                    continue;
                }
            }
            $positioningArray[$position] = $blockName;
        }

        return sort($positioningArray);
    }

    /**
     * To be written
     *
     * @param Mage_Core_Model_Layout $layout
     * @return array | boolean
     */
    private function renderOccupyNext($layout)
    {
        /* @var $toolbar Mage_Catalog_Block_Product_List_Toolbar */
        $toolbar = $layout->getBlock('product_list_toolbar');
        if(!$toolbar) {
            return false;
        }

        $positioningArray = [];
        $priorityArray = Mage::helper('itembanner')->getBannerPriorityArray();
        $positionField = sprintf('position_in_%s', $toolbar->getCurrentMode());
        $maxNum = 3 * Mage::getStoreConfig(sprintf('catalog/frontend/%s_per_page', $toolbar->getCurrentMode()));
        foreach (Potoky_ItemBanner_Block_Banner::$allOfTheType as $blockName) {
            $block = $layout->getBlock($blockName);
            $position = $block->getData($positionField);

            if (!$block->getData('is_active')) {
                continue;
            }

            if ($position > $maxNum) {
                continue;
            }

            $occupyingBlockName = $positioningArray[$position];
            while ($occupyingBlockName) {
                $occupyingBlockId = $layout->getBlock($occupyingBlockName)->getData('instance_id');
                $wishingBlockId = $block-> getData('instance_id');
                if ($priorityArray[$occupyingBlockId] < $priorityArray[$wishingBlockId]) {
                    if ($position + 1 <= $maxNum) {
                        $occupyingBlockName = $positioningArray[$position++];
                        continue;
                    } else {
                        continue 2;
                    }
                } else {
                    if ($position + 1 <= $maxNum) {
                        $positioningArray[$position] = $blockName;
                        $blockName = $occupyingBlockName;
                        $block = $layout->getBlock($blockName);
                        $occupyingBlockName = $positioningArray[$position++];
                        continue;
                    } else {
                        continue 2;
                    }
                }
            }
            $positioningArray[$position] = $blockName;
        }

        return sort($positioningArray);
    }
}