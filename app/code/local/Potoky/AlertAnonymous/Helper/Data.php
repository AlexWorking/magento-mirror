<?php

class Potoky_AlertAnonymous_Helper_Data extends Mage_Core_Helper_Abstract
{
    public static $helpers = [
        'data'     => 'alertanonymous',
        'allow'    => 'alertanonymous/allow',
        'login'    => 'alertanonymous/login',
        'registry' => 'alertanonymous/registry',
        'data_1'   => 'core',
        'data_2'   => 'anonymouscustomer',
        'entity'   => 'anonymouscustomer/entity',
    ];
    private $controllerMapping = [
        'Potoky_AlertAnonymous_AddController'         => ['allow', 'login', 'entity', 'registry'],
        'Potoky_AlertAnonymous_UnsubscribeController' => ['data_1', 'entity', 'registry']
    ];

    private $modelAndBlockMapping = [
        'Potoky_AlertAnonymous_Model_Email_Template'  => ['registry'],
        'Potoky_AlertAnonymous_Model_Customer'        => ['registry'],
        'Potoky_AlertAnonymous_Model_Email'           => ['data_1'],
        'Potoky_AlertAnonymous_Model_Observer'        => ['registry', 'entity'],
        'Potoky_AlertAnonymous_Model_Price'           => ['registry'],
        'Potoky_AlertAnonymous_Block_Product_View'    => ['allow', 'login']
    ];

    public function initHelpers(){
        foreach (self::$helpers as $name => $uri)
        {
            $class = Mage::helper($uri);
        }
    }

    public function setUpHelpers($controller = null)
    {
        if ($controller) {
            foreach ($this->controllerMapping[$controller] as $helperName) {
                $controller::$helpers[$helperName] = & self::$helpers[$helperName];
            }
        }

        foreach ($this->modelAndBlockMapping as $className) {
            foreach ($className as $helperName) {
                $className::$helpers[$helperName] = & self::$helpers[$helperName];
            }
        }
    }
}