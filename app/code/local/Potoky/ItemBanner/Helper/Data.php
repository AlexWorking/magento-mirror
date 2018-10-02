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
        $items = $collection->getItems();
        for ($counter = 0; $counter < $count; $counter++) {
            $priorityArray[$items[$counter]->getId()] = $counter;
        }

        return $priorityArray;
    }
}