<?php

class Potoky_ItemBanner_Helper_Data extends Mage_Core_Helper_Abstract
{
    private static $currentInstance;

    private static $errorMessages = [
        'image' => 'At least one original size of the image is less than 800 px. The Image has not been saved',
        'position_in_grid' => 'Position of the banner for the Grid mode is not correctly defined.',
        'position_in_list' => 'Position of the banner for the List mode is not correctly defined.',
        'rel_coords_grid' => 'Grid mode selection is not defined.',
        'rel_coords_list' => 'List mode selection is not defined.',
        'title' => 'The title for the banner popup is empty.',
        'description' => 'The description for the banner popup is empty.',
        'link' => 'The link for the banner popup is empty or incorrect.'
    ];

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

    public function geterrorMessage($field)
    {
        return self::$errorMessages[$field];
    }

    public function getCurrentInstance()
    {
        if (!self::$currentInstance) {
            self::$currentInstance = Mage::registry('current_widget_instance');
        }

        return self::$currentInstance;
    }
}