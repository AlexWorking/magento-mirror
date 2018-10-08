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
        $psitioningArray = false;
        switch ($option) {
            case 1:
                $psitioningArray = $this->renderFirstOccupy($layout);
                break;
            case 2:
                $psitioningArray = $this->renderMoveToNext($layout);
                break;
        }

        if($psitioningArray) {
            Mage::unregister('potoky_itembanner_psitioningArray');
            Mage::register('potoky_itembanner_psitioningArray', $psitioningArray);
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
        foreach (Potoky_ItemBanner_Block_Banner::$allOfTheType as $key => $blockName) {
            $block = $layout->getBlock($blockName);
            if(!$block->getData('is_active')) {
                $layout->unsetBlock($blockName);
                unset(Potoky_ItemBanner_Block_Banner::$allOfTheType[$key]);
                continue;
            }

            $position = $block->getData($positionField);
            if ($occupying = $positioningArray[$position]) {
                $occupying = $layout->getBlock($occupying)->getData('instance_id');
                $wishing = $block-> getData('instance_id');
                if($priorityArray[$occupying] < $priorityArray[$wishing]) {
                    $layout->unsetBlock($blockName);
                    unset(Potoky_ItemBanner_Block_Banner::$allOfTheType[$key]);
                    continue;
                }
            }
            $positioningArray[$position] = $blockName;
        }

        return $positioningArray;
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
        $positionMax = Mage::getStoreConfig('catalog/frontend/grid_per_page');
        foreach (Potoky_ItemBanner_Block_Banner::$allOfTheType as $key => $blockName) {
            $block = $layout->getBlock($blockName);
            if(!$block->getData('is_active')) {
                $layout->unsetBlock($blockName);
                unset(Potoky_ItemBanner_Block_Banner::$allOfTheType[$key]);
                continue;
            }

            $position = $block->getData($positionField);
            if ($occupying = $positioningArray[$position]) {
                $occupying = $layout->getBlock($occupying)->getData('instance_id');
                $wishing = $block-> getData('instance_id');
                if ($priorityArray[$occupying] < $priorityArray[$wishing]) {
                    if ($position + 1 <= $positionMax) {
                        $positioningArray[$position + 1] = $blockName;
                    } else {
                        $layout->unsetBlock($blockName);
                        unset(Potoky_ItemBanner_Block_Banner::$allOfTheType[$key]);
                    }
                    continue;
                } else {
                    if ($position + 1 <= $positionMax) {
                        $positioningArray[$position + 1] = $positioningArray[$position];
                    } else {
                        $layout->unsetBlock($positioningArray[$position]);
                        unset(Potoky_ItemBanner_Block_Banner::$allOfTheType[$key]);
                    }
                }
            }
            $positioningArray[$position] = $blockName;
        }

        return $positioningArray;
    }
}