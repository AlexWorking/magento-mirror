<?php

class Potoky_ItemBanner_Block_Adminhtml_Widget_Cropped extends Mage_Adminhtml_Block_Template
{
    public function _prepareImage($mode) {
        $baseDir = Mage::getBaseDir('media');
        $path = $baseDir . DS . 'itembanner' . DS . $mode . DS . 'Small-mario.png';
        $path = str_replace($baseDir . DS, "", $path);

        return Mage::getBaseUrl('media') . str_replace(DS, '/', $path);
    }
}