<?php

class Potoky_ItemBanner_Block_Banner extends Mage_Core_Block_Abstract implements Mage_Widget_Block_Interface
{
    protected function _toHtml() {
        $html = '<h1>MY WIDGET</h1>';
        return $html;
    }
}