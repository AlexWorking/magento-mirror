<?php

class Potoky_Itembanner_Model_Widget_Instance extends Mage_Widget_Model_Widget_Instance
{
    /**
     * Layout Update XML array
     *
     * @var string
     */
    protected $xmlUpdates;

    public function generateLayoutUpdateXml($blockReference, $templatePath = '')
    {
        $parent = parent::generateLayoutUpdateXml($blockReference, $templatePath);

        $this->xmlUpdates .= $parent;

        return $parent;
    }

    /**
     * To be written
     *
     */
    protected function _beforeSave()
    {
        $parent = parent::_beforeSave();

        if($this->getType() == "itembanner/banner") {
            $this->addSearchHandles()->deactivate();
        }

        return $parent;
    }

    protected function _afterSave()
    {
        $parent =  parent::_afterSave();

        if($this->getType() != "itembanner/banner") {
            return $parent;
        }

        $itemBannerInfo = Mage::getModel('itembanner/bannerinfo');
        $xmlObject = simplexml_load_string(
            sprintf('<wrapper>%s</wrapper>', $this->xmlUpdates),
            'Varien_Simplexml_Element');
        $elements = $xmlObject->xpath('//block');
        $namesInLayout = [];
        foreach ($elements as $element) {
            $namesInLayout[] = $element->getAttribute('name');
        }
        $namesInLayout = serialize($namesInLayout);

        if ($this->isObjectNew()) {
            $itemBannerInfo->setData('instance_id', $this->getId());
        } else {
            $itemBannerInfo->load($this->getId(), 'instance_id');
            if ($itemBannerInfo->getId() && !$itemBannerInfo->getIsActive()) {
                $itemBannerInfo->setData('is_active', true);
            }
        }
        $itemBannerInfo->setData('names_in_layout', $namesInLayout);
        $itemBannerInfo->save();

        return $parent;
    }

    private function addSearchHandles()
    {
        $pageGroups = $this->getData('page_groups');
        foreach ($pageGroups as &$pageGroup) {
            if (in_array('catalog_category_layered', $pageGroup['layout_handle_updates']) ||
                in_array('catalog_category_default', $pageGroup['layout_handle_updates'])) {
                $pageGroup['layout_handle_updates'][] = 'catalogsearch_result_index';
                $pageGroup['layout_handle_updates'][] = 'catalogsearch_advanced_result';
            }
        }
        unset($pageGroup);
        $this->setData('page_groups', $pageGroups);

        return $this;
    }

    private function deactivate()
    {
        $itemBannerInfo = Mage::helper('itembanner')->getActiveInstanceInfo();

        if ($itemBannerInfo->getId()) {
            $activeInstanceId = $itemBannerInfo->getInstanceId();
            if ($this->getWidgetParameters()['is_active'] &&
                $this->getId() != $activeInstanceId) {
                $itemBannerInfo->setData('is_active', false);
                $itemBannerInfo->save();
                /* @var $widgetInstance Mage_Widget_Model_Widget_Instance */
                $widgetInstance = Mage::getModel('widget/widget_instance')->load($activeInstanceId);
                $parameters = $widgetInstance->getWidgetParameters();
                $parameters['is_active'] = 0;
                $widgetInstance->setData('widget_parameters', $parameters);
                $widgetInstance->save();
            }
        }

        return $this;
    }
}