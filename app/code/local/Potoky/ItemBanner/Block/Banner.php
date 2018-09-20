<?php

class Potoky_ItemBanner_Block_Banner extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface
{
    public function setTemplate($template)
    {
        if (!$this->getParentBlock() instanceof Mage_Catalog_Block_Product_List ||
            !Mage::helper('itembanner')->getActiveInstanceInfo()) {
            $template = '';
        }
        return parent::setTemplate($template);
    }
}