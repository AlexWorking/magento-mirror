<?php

class Potoky_ItemBanner_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * An associative array with instance parameters and corresponding error messages used in
     * activeness eligibility validation
     *
     * @var array
     */
    private static $errorMessages = [
        'image' => 'At least one original size of the image is less than 800 px. The New Image has not been saved.',
        'position_in_grid' => 'Position of the banner for the Grid mode is not correctly defined. Must by a natural number.',
        'position_in_list' => 'Position of the banner for the List mode is not correctly defined. Must by a natural number.',
        'rel_coords_grid' => 'Grid mode selection is not defined.',
        'rel_coords_list' => 'List mode selection is not defined.',
        'title' => 'The title for the banner popup is empty.',
        'description' => 'The description for the banner popup is empty.',
        'link' => 'The link for the banner popup is empty or incorrect.'
    ];

    /**
     * Return an associative array with instance ids and corresponding priority positions
     *
     * @return array
     */
    public function getBannerPriorityArray()
    {
        $collection = Mage::getModel('widget/widget_instance')
            ->getCollection()
            ->addFieldToFilter('instance_type', 'itembanner/banner')
            ->setOrder('sort_order', 'ASC');

        $priorityArray = [];
        $counter = 1;

        foreach ($collection as $item) {
            $priorityArray[$item->getId()] = $counter++;
        }

        return $priorityArray;
    }

    /**
     * Build url or path to the file using passed in arguments
     *
     * @param $fileName
     * @param string $mode
     * @param bool $isTypeUrl
     * @return mixed|string
     */
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

    /**
     * Return corresponding error message to field requested
     *
     * @param $field
     * @return mixed
     */
    public function geterrorMessage($field)
    {
        return self::$errorMessages[$field];
    }
}