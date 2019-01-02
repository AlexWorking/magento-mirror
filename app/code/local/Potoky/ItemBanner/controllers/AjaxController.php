<?php

class Potoky_ItemBanner_AjaxController extends Mage_Core_Controller_Front_Action
{
    public function clickincrementAction()
    {
        $instanceId = $this->getRequest()->getParam('instanceId');
        $widgetInstance = Mage::getModel('widget/widget_instance')->load($instanceId);
        $widgetInstance->setDataChanges(true);
        Potoky_ItemBanner_Model_Observer::setSaveWithoutController(true);

        $widgetInstance->save();
    }
}