<?php

require_once(
    Mage::getModuleDir('controllers','Mage_Widget') .
    DS . 'Adminhtml' .
    DS . 'Widget' .
    DS . 'InstanceController.php');
class Potoky_ItemBanner_Adminhtml_Widget_InstanceController extends Mage_Widget_Adminhtml_Widget_InstanceController
{
    public function saveAction()
    {
        die('rewritten');
        return parent::saveAction(); // TODO: Change the autogenerated stub
    }
}