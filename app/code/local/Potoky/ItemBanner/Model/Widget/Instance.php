<?php

/**
 * Created by PhpStorm.
 * User: light
 * Date: 9/13/2018
 * Time: 7:02 PM
 */
class Potoky_Itembanner_Model_Widget_Instance extends Mage_Widget_Model_Widget_Instance
{
    const CATALOG_SEARCH_LAYOUT_HANDLE    = 'catalogsearch_result_index';
    const CATALOG_SEARCH_ADVANCED_LAYOUT_HANDLE    = 'catalogsearch_advanced_result';

    protected function _construct()
    {
        parent::_construct();
        $this->_layoutHandles['catalog_search'] = self::CATALOG_SEARCH_LAYOUT_HANDLE;
        $this->_layoutHandles['catalog_search_advanced'] = self::CATALOG_SEARCH_ADVANCED_LAYOUT_HANDLE;
    }
}