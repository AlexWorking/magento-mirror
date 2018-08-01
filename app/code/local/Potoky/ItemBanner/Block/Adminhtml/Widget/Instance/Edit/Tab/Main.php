<?php

class Potoky_ItemBanner_Block_Adminhtml_Widget_Instance_Edit_Tab_Main extends Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Main
{
    protected function _prepareForm()
    {
        $parent = parent::_prepareForm();
        $form = $parent->getForm();
        $fieldset = $form->addFieldSet('banner_related_properties',
            array('legend' => Mage::helper('itembanner')->__('Banner Related Properties'))
        );
        $fieldset->addField('banner_image', 'text', array(
            'name'  => 'image',
            'label' => Mage::helper('widget')->__('Banner Image'),
            'title' => Mage::helper('widget')->__('Banner Image'),
            'class' => '',
            'required' => true,
            'note' => Mage::helper('widget')->__('Image that will cover the whole banner')
        ));
        return $parent;
    }
}