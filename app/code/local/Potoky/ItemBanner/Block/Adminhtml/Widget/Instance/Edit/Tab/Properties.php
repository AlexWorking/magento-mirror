<?php

class Potoky_ItemBanner_Block_Adminhtml_Widget_Instance_Edit_Tab_Properties extends Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Properties
{
    /*protected function _prepareForm()
    {
        $parent = parent::_prepareForm();
        $widgetInstance = $this->getWidgetInstance();
        $form = $parent->getForm();
        $fieldset = $form->addFieldSet('banner_related_properties',
            array('legend' => Mage::helper('itembanner')->__('Banner Related Properties'))
        );
        $fieldset->addField('banner_image', 'file', array(
            'name' => 'image',
            'label' => Mage::helper('widget')->__('Banner Image'),
            'title' => Mage::helper('widget')->__('Banner Image'),
            'class' => '',
            'required' => true,
            'note' => Mage::helper('widget')->__('Image that will cover the whole banner')
        ));
        $layoutBlock = $this->getLayout()
            ->createBlock('widget/adminhtml_widget_instance_edit_tab_main_layout')
            ->setWidgetInstance($widgetInstance);
        $form->getElement('banner_image')->setRenderer($layoutBlock);

        return $parent;
    }*/

    /**
     * Fieldset getter/instantiation
     *
     * @return Varien_Data_Form_Element_Fieldset
     */
    public function getMainFieldset()
    {
        $parent = parent::getMainFieldset();
        if ($this->getWidgetType() == 'itembanner/banner') {
            $parent->addType('image', 'Potoky_ItemBanner_Block_Adminhtml_Widget_Helper_Image');
        }

        return $parent;
    }
}