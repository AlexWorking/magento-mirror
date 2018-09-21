<?php

class Potoky_ItemBanner_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @var Potoky_ItemBanner_Model_Bannerinfo
     */
    public $activeInstanceInfo;

    public function getActiveInstanceInfo()
    {
        if (!isset($this->activeInstanceInfo)) {
            $this->activeInstanceInfo = Mage::getModel('itembanner/bannerinfo')->load(true, 'is_active');
        }
        return $this->activeInstanceInfo;
    }

    public function getNamesOfActiveBlock()
    {
        return $this->getActiveInstanceInfo()->getData('names_in_layout');
    }
}