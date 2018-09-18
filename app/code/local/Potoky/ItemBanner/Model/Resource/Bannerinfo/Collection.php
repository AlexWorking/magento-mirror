<?php

class Potoky_ItemBanner_Model_Resource_Bannerinfo_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('itembanner/bannerinfo');
    }
}