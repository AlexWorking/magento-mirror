<?php

class Potoky_ItemBanner_Model_Resource_Bannerinfo extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('itembanner/bannerinfo', 'bannerinfo_id');
    }
}