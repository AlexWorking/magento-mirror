<?php

class Potoky_ItemBanner_Model_Observer
{
    private static $saveWithoutController;

    public static function setSaveWithoutController($boolean)
    {
        self::$saveWithoutController = $boolean;
    }

    /**
     * To be written
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function additionalBeforeSave(Varien_Event_Observer $observer)
    {
        /* @var $widgetInstance Mage_Widget_Model_Widget_Instance */
        $widgetInstance = $observer->getEvent()->getObject();

        if ($widgetInstance->getType() != "itembanner/banner") {

            return $this;
        }

        if (self::$saveWithoutController) {
            $origPageGroups = $widgetInstance->getOrigData('page_groups');
            $pageGroups = [];
            $pageGroupIds = [];
            $parameters = $widgetInstance->getWidgetParameters();
            foreach ($origPageGroups as $number => $origPageGroup) {
                $pageGroups[$number]['page_id'] = $origPageGroup['page_id'];
                $pageGroups[$number]['group'] = $origPageGroup['page_group'];
                $pageGroups[$number]['layout_handle'] = $origPageGroup['layout_handle'];
                $pageGroups[$number]['block_reference'] = $origPageGroup['block_reference'];
                $pageGroups[$number]['for'] = $origPageGroup['page_for'];
                $pageGroups[$number]['entities'] = $origPageGroup['entities'];
                $pageGroups[$number]['template'] = $origPageGroup['page_template'];
                $pageGroups[$number]['layout_handle_updates'][] = $origPageGroup['layout_handle'];

                if (in_array('catalog_category_layered', $pageGroups[$number]['layout_handle_updates']) ||
                    in_array('catalog_category_default', $pageGroups[$number]['layout_handle_updates'])) {
                    $pageGroups[$number]['layout_handle_updates'][] = 'catalogsearch_result_index';
                    $pageGroups[$number]['layout_handle_updates'][] = 'catalogsearch_advanced_result';
                }

                $pageGroupIds[] = $origPageGroup['page_id'];
            }
            $parameters['goto']++;
            $widgetInstance->setData('widget_parameters', $parameters);
            $widgetInstance->setData('page_group_ids', $pageGroupIds);
            unset($parameters);
        } else {
            $pageGroups = $widgetInstance->getData('page_groups');
            foreach ($pageGroups as &$pageGroup) {
                if (in_array('catalog_category_layered', $pageGroup['layout_handle_updates']) ||
                    in_array('catalog_category_default', $pageGroup['layout_handle_updates'])) {
                    $pageGroup['layout_handle_updates'][] = 'catalogsearch_result_index';
                    $pageGroup['layout_handle_updates'][] = 'catalogsearch_advanced_result';
                }
            }
            unset($pageGroup);
        }

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

        /* @var $toolbar Mage_Catalog_Block_Product_List_Toolbar */
        $toolbar = $layout->getBlock('product_list_toolbar');
        if(!$toolbar) {
            return $this;
        }

        $option = Mage::getStoreConfig('cms/itembanner/rendering_type');
        $positioningArray = false;
        switch ($option) {
            case 1:
                $positioningArray = $this->renderPriorOccupy($layout, $toolbar->getCurrentMode());
                break;
            case 2:
                $positioningArray = $this->renderOccupyNext($layout, $toolbar->getCurrentMode());
                break;
        }

        if($positioningArray) {
            $count = count($positioningArray);
            $firstNum = ($toolbar->getCurrentPage() - 1) * $toolbar->getLimit() + 1;
            $lastNum = $firstNum - 1 + (int) $toolbar->getLimit();
            $previousPagesBannerQty = 0;
            $positions = array_keys($positioningArray);
            foreach ($positions as $position) {
                if($position < $firstNum) {
                    unset($positioningArray[$position]);
                    $previousPagesBannerQty++;
                }
                elseif ($position > $lastNum) {
                    unset($positioningArray[$position]);
                }
            }
            Mage::unregister('potoky_itembanner');
            Mage::register('potoky_itembanner', [
                'count'                  => $count,
                'previousPagesBannerQty' => $previousPagesBannerQty,
                'positioningArray'       => $positioningArray,
                'mode'                   => $toolbar->getCurrentMode()
            ]);
        }

        return $this;
    }

    /**
     * To be written
     *
     * @param Mage_Core_Model_Layout $layout
     * @return array | boolean
     */
    private function renderPriorOccupy($layout, $mode)
    {
        $priorityArray = Mage::helper('itembanner')->getBannerPriorityArray();
        $positioningArray = [];
        $positionField = sprintf('position_in_%s', $mode);
        $maxNum = 3 * Mage::getStoreConfig(sprintf('catalog/frontend/%s_per_page', $mode));
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

        return $positioningArray;
    }

    /**
     * To be written
     *
     * @param Mage_Core_Model_Layout $layout
     * @return array | boolean
     */
    private function renderOccupyNext($layout, $mode)
    {
        $positioningArray = [];
        $priorityArray = Mage::helper('itembanner')->getBannerPriorityArray();
        $positionField = sprintf('position_in_%s', $mode);
        $maxNum = 3 * Mage::getStoreConfig(sprintf('catalog/frontend/%s_per_page', $mode));
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
                        $occupyingBlockName = $positioningArray[++$position];
                        continue;
                    } else {
                        continue 2;
                    }
                } else {
                    if ($position + 1 <= $maxNum) {
                        $positioningArray[$position] = $blockName;
                        $blockName = $occupyingBlockName;
                        $block = $layout->getBlock($blockName);
                        $occupyingBlockName = $positioningArray[++$position];
                        continue;
                    } else {
                        continue 2;
                    }
                }
            }
            $positioningArray[$position] = $blockName;
        }

        return $positioningArray;
    }
}