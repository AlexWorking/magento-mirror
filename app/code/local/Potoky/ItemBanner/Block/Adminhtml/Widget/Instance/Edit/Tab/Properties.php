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
            $parent->addType('ib_image', 'Potoky_ItemBanner_Block_Adminhtml_Widget_Helper_Image');
            $imageBlock = $this->getLayout()->createBlock('itembanner/adminhtml_widget_cropped');
            $this->setChild('itembanner_cropped', $imageBlock);
            $this->setTemplate('itembanner/form.phtml');
        }

        return $parent;
    }

    /**
     * Add field to Options form based on option configuration
     *
     * @param Varien_Object $parameter
     * @return Varien_Data_Form_Element_Abstract
     */
    protected function _addField($parameter)
    {
        if ($parent = parent::_addField($parameter)) {
            if ($parent->getData('name') === 'parameters[goto]') {
                $parent->setData('readonly', 'readonly');
            }
        }

        return $parent;
    }
}