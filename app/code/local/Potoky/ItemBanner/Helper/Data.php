<?php

class Potoky_ItemBanner_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function toBeDeactivated($field = null)
    {
        $collection = Mage::getModel('itembanner/bannerinfo')->getCollection();
        if (in_array($field, ['grid', 'list'])) {
            $toBeDeactivated = $collection
                ->addFieldToFilter('position_in_' . $field)
                ->addFieldToFilter('active_for_' . $field)
                ->getFirstItem();
        }
        elseif ($field) {
            $toBeDeactivated = $collection
                ->addFieldToFilter('is_active');
        } else {
            return false;
        }

        return $toBeDeactivated;
    }

    public function getNamesOfActiveBlock()
    {
        return $this->toBeDeactivated()->getData('names_in_layout');
    }
}