<?php

/**
 * Created by PhpStorm.
 * User: light
 * Date: 9/13/2018
 * Time: 5:05 PM
 */
class Potoky_Itembanner_Block_Adminhtml_Widget_Instance_Edit_Tab_Main_Layout
    extends Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Main_Layout
{
    protected function _getDisplayOnOptions()
    {
        $parent = parent::_getDisplayOnOptions();
        //if (Mage::registry('current_widget_instance') &&
        //    Mage::registry('current_widget_instance')->getType() == 'itembanner/banner') {
            $forInsert[] = array(
                'label' => 'Search',
                'value' => array(
                    array(
                        'value' => 'catalog_search',
                        'label' => $this->helper('core')->jsQuoteEscape(Mage::helper('itembanner')->__('Search Results'))
                    ),
                    array(
                        'value' => 'catalog_search_advanced',
                        'label' => $this->helper('core')->jsQuoteEscape(Mage::helper('itembanner')->__('Advance Search Results'))
                    )
                )
            );
            array_splice($parent,2, 0, $forInsert);
        //}

        return $parent;
    }

    public function getDisplayOnContainers()
    {
        $parent = parent::getDisplayOnContainers();
        $parent['search'] = array(
            'label' => 'Categories',
            'code' => 'categories',
            'name' => 'catalog_search',
            'layout_handle' => 'catalogsearch_result_index',
            'is_anchor_only' => '',
            'product_type_id' => ''
        );
        $parent['advanced_search'] = array(
            'label' => 'Categories',
            'code' => 'categories',
            'name' => 'catalog_search_advanced',
            'layout_handle' => 'catalogsearch_advanced_result',
            'is_anchor_only' => '',
            'product_type_id' => ''
        );

        return $parent;
    }
}