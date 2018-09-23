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
}