<?php

class Potoky_ItemBanner_Block_Adminhtml_Widget_Helper_Editor extends Varien_Data_Form_Element_Editor
{
    public function getHtmlAttributes()
    {
        $parent = parent::getHtmlAttributes();
        $parent[] = 'maxlength';

        return $parent;
    }
}