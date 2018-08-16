<?php

class Potoky_ItemBanner_Block_Adminhtml_Widget_Helper_Image extends Varien_Data_Form_Element_Image
{
    /**
     * Get image preview url
     *
     * @return string
     */
    protected function _getUrl()
    {
        if ($parent = $this->getValue()) {
            $parent = 'itembanner' . '/' . $parent;
        }

        return $parent;
    }

    protected function _getDeleteCheckbox()
    {
        if ($this->getName() == 'parameters[image]' &&
            $this->getLabel() == 'Image For Banner') {
            return '';
        }

        return parent::_getDeleteCheckbox();
    }
}