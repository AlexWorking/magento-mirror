<?php

class Potoky_ItemBanner_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getBannerPriorityArray()
    {
        $collection = Mage::getModel('widget/widget_instance')
            ->getCollection()
            ->addFieldToFilter('instance_type', 'itembanner/banner')
            ->setOrder('sort_order', 'ASC');
        $count = count($collection);

        $priorityArray = [];
        $counter = 1;

        foreach ($collection as $item) {
            $priorityArray[$item->getId()] = $counter++;
        }

        return $priorityArray;
    }
}