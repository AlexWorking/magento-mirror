<?php

class Potoky_ItemBanner_Helper_Data extends Mage_Core_Helper_Abstract
{
    private static $currentInstance;

    private static $errorsRelatedArray = [
        'position_in_grid' => [
            null,
            'Position of the banner for the Grid mode is not correctly defined.'
        ],
        'position_in_list' => [
            null,
            'Position of the banner for the List mode is not correctly defined.'
        ],
        'rel_coords_grid' => [
            null,
            'Grid mode selection is not defined.'
        ],
        'rel_coords_list' => [
            null,
            'List mode selection is not defined.'
        ],
        'title' => [
            null,
            'The title for the banner popup is empty.'
        ],
        'description' => [
            null,
            'The description for the banner popup is empty.'
        ],
        'link' => [
            null,
            'The link for the banner popup is empty or incorrect.'
        ]
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

    public function getErrorsRelatedData($field = null)
    {
        if (current(self::$errorsRelatedArray)[0] === null) {
            foreach (self::$errorsRelatedArray as $error) {
                $error[0] = false;
                $error[1] = $this->__($error);
            }
        }

        if ($field) {
            return self::$errorsRelatedArray[$field];
        }

        return self::$errorsRelatedArray;
    }

    public function getCurrentInstance()
    {
        if (!self::$currentInstance) {
            self::$currentInstance = Mage::registry('current_widget_instance');
        }

        return self::$currentInstance;
    }
}