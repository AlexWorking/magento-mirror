<?php

class Potoky_ItemBanner_Block_Banner extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface
{
    /**
     * All the blocks of this type initiated in layout
     *
     * @var array
     */
    public static $allOfTheType = [];

    public static $imageSizes = [
        'grid' => [
            'width'  => 210,
            'height' => null
        ],
        'list' => [
            'width'  => 960,
            'height' => null
        ],
    ];

    public function setNameInLayout($name)
    {
        self::$allOfTheType[] = $name;

        return parent::setNameInLayout($name);
    }

    public static function setMode($mode)
    {
        self::$mode = $mode;
    }

    public function _getImageUrl($path)
    {
        $baseDir = Mage::getBaseDir('media');
        $path = str_replace($baseDir . DS, "", $path);
        return Mage::getBaseUrl('media') . str_replace(DS, '/', $path);
    }

    public function _prepareImage()
    {
        $mode = (Mage::registry('potoky_itembanner')['mode']) ? Mage::registry('potoky_itembanner')['mode'] : '';
        $newFilePath = Mage::getBaseDir('media') . DS . 'itembanner' . DS . $mode . DS . $this->getData('image');
        //if(!file_exists($newFilePath)) {
            $image = new Varien_Image(Mage::getBaseDir('media') . DS . 'itembanner' . DS . $this->getData('image'));
            //$sizes = $this->getResizeSizes([$image->getOriginalWidth(), $image->getOriginalHeight()], $mode);
            $image->keepTransparency(true);
            $image->resize(self::$imageSizes[$mode]['width'], self::$imageSizes[$mode]['height']);
            $image->save($newFilePath);
        //}

        return $this->_getImageUrl($newFilePath);
    }

    private function getResizeSizes(Varien_Image $image, $mode)
    {
        if ($mode == 'grid') {
            $width = $image->getOriginalWidth();
        }
    }
}