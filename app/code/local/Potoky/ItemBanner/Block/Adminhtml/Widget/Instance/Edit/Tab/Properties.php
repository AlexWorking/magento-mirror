<?php

class Potoky_ItemBanner_Block_Adminhtml_Widget_Instance_Edit_Tab_Properties extends Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Properties
{
    /**
     * Fieldset getter/instantiation
     *
     * @return Varien_Data_Form_Element_Fieldset
     */
    public function getMainFieldset()
    {
        if ($this->_getData('main_fieldset') instanceof Varien_Data_Form_Element_Fieldset) {
            return $this->_getData('main_fieldset');
        }

        $parent = parent::getMainFieldset();

        if ($this->getWidgetType() == 'itembanner/banner') {
            $parent->addType('image', 'Potoky_ItemBanner_Block_Adminhtml_Widget_Helper_Image');
            $variablesBlock = $this->getLayout()->createBlock('adminhtml/template');
            $variablesBlock->setTemplate('itembanner/variables.phtml');
            $imageBlock = $this->getLayout()->createBlock('itembanner/adminhtml_widget_cropped');
            $imageBlock->setTemplate('itembanner/cropped.phtml');
            $this->setChild('itembanner_variables', $variablesBlock);
            $this->setChild('itembanner_cropped', $imageBlock);
            $this->setTemplate('itembanner/form.phtml');
        }

        return $parent;
    }
}