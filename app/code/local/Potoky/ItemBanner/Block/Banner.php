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

    public function _prepareImage()
    {
        $mode = (Mage::registry('potoky_itembanner')['mode']) ? Mage::registry('potoky_itembanner')['mode'] : '';
        $baseDir = Mage::getBaseDir('media');
        $path = $baseDir . DS . 'itembanner' . DS . $mode . DS . $this->getData('image');
        $path = str_replace($baseDir . DS, "", $path);

        return Mage::getBaseUrl('media') . str_replace(DS, '/', $path);
    }
}