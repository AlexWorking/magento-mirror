<?php

class Potoky_ItemBanner_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @var Potoky_Itembanner_Model_Widget_Instance
     */
    public $activeInstance;

    public function getActiveInstanceInfo()
    {
        if (!isset($this->activeInstance)) {
            $this->activeInstance = Mage::getModel('itembanner/bannerinfo')->load(true, 'is_active');
        }
        return $this->activeInstance;
    }

    public function getNamesOfActiveBlock()
    {
        return $this->getActiveInstanceInfo()->getData('names_in_layout');
    }
}