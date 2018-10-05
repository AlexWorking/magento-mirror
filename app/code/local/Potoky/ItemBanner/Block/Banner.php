<?php

class Potoky_ItemBanner_Block_Banner extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface
{
    /**
     * All the blocks of this type initiated in layout
     *
     * @var array
     */
    public static $allOfTheType = [];

    public function setNameInLayout($name)
    {
        self::$allOfTheType[] = $name;

        return parent::setNameInLayout($name);
    }

    public function _getImageUrl($path)
    {
        $baseDir = Mage::getBaseDir('media');
        $path = str_replace($baseDir . DS, "", $path);
        return Mage::getBaseUrl('media') . str_replace(DS, '/', $path);
    }

    public function _prepareImage()
    {
        $newFilePath = Mage::getBaseDir('media') . DS . 'itembanner' . DS . 'R--' . $this->getData('image');
        if(!file_exists($newFilePath)) {
            $image = new Varien_Image(Mage::getBaseDir('media') . DS . 'itembanner' . DS . $this->getData('image'));
            $image->resize(300, 300);
            $image->save($newFilePath);
        }

        return $this->_getImageUrl($newFilePath);
    }
}