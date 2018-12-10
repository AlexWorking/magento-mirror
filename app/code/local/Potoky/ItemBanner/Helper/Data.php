<?php

class Potoky_ItemBanner_Helper_Data extends Mage_Core_Helper_Abstract
{
    private $origDimensions = [];

    private static $currentInstance;

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

    public function getImageUri($fileName, $mode = '', $isTypeUrl = true)
    {
        $baseDir = Mage::getBaseDir('media');
        $mode = ($mode) ? DS . $mode : $mode;
        $path = $baseDir . DS . 'itembanner' . $mode . DS . $fileName;

        if (!$isTypeUrl) {
            return $path;
        }

        $path = str_replace($baseDir . DS, "", $path);

        return Mage::getBaseUrl('media') . str_replace(DS, '/', $path);
    }

    public function getCurrentInstance()
    {
        if (!self::$currentInstance) {
            self::$currentInstance = Mage::registry('current_widget_instance');
        }

        return self::$currentInstance;
    }
}