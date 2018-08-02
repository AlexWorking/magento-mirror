<?php

class Potoky_ItemBanner_Block_Banner extends Mage_Core_Block_Abstract implements Mage_Widget_Block_Interface
{
    protected function _toHtml() {
        $html = '<img src="' . Mage::getBaseUrl("media") . 'itembanner/Lioness-Roar-PNG-Pic.png" alt="HHHHH">';
        return $html;
    }
}