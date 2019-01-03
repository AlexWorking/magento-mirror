<?php

class Potoky_ItemBanner_AjaxController extends Mage_Core_Controller_Front_Action
{
    public function clickincrementAction()
    {
        $instanceId = $this->getRequest()->getParam('instanceId');

        if (!$instanceId) {

            return;
        }

        /* @var $widgetInstance Mage_Widget_Model_Widget_Instance */
        $widgetInstance = Mage::getModel('widget/widget_instance')->load($instanceId);

        if (!$widgetInstance) {

            return;
        }

        $parameters = $widgetInstance->getWidgetParameters();
        $parameters['goto']++;
        $widgetInstance->setData('widget_parameters', $parameters);
        Potoky_ItemBanner_Model_Observer::setSaveWithoutController(true);

        $widgetInstance->save();
    }
}