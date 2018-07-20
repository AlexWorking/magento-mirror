<?php

class Potoky_AlertAnonymous_Helper_Data extends Mage_Core_Helper_Abstract
{
    public static $helpers = [];

    private static $classHelpers = [
        'Potoky_AlertAnonymous_AddController'         => [
            'allow'    => 'alertanonymous/allow',
            'login'    => 'alertanonymous/login',
            'entity'   => 'anonymouscustomer/entity',
            'registry' => 'alertanonymous/registry'
        ],
        'Potoky_AlertAnonymous_UnsubscribeController' => [
            'data_1'   => 'core',
            'entity'   => 'anonymouscustomer/entity',
            'registry' => 'alertanonymous/registry'
        ],
        'Potoky_AlertAnonymous_Model_Email_Template'  => [
            'registry' => 'alertanonymous/registry'
        ],
        'Potoky_AlertAnonymous_Model_Customer'        => [
            'registry' => 'alertanonymous/registry'
        ],
        'Potoky_AlertAnonymous_Model_Email'           => [
            'data_1'   => 'core'
        ],
        'Potoky_AlertAnonymous_Model_Observer'        => [
            'registry' => 'alertanonymous/registry',
            'entity'   => 'anonymouscustomer/entity'
        ],
        'Potoky_AlertAnonymous_Model_Price'           => [
            'registry' => 'alertanonymous/registry'
        ],
        'Potoky_AlertAnonymous_Block_Product_View'    => [
            'allow'    => 'alertanonymous/allow',
            'login'    => 'alertanonymous/login'
        ]
    ];

    private static $classHelpersMirror = [
        'Potoky_AlertAnonymous_AddController'         => [
            'allow',
            'login',
            'entity',
            'registry'
        ],
        'Potoky_AlertAnonymous_UnsubscribeController' => [
            'data_1',
            'entity',
            'registry'
        ],
        'Potoky_AlertAnonymous_Model_Email_Template'  => [
            'registry'
        ],
        'Potoky_AlertAnonymous_Model_Customer'        => [
            'registry'
        ],
        'Potoky_AlertAnonymous_Model_Email'           => [
            'data_1'
        ],
        'Potoky_AlertAnonymous_Model_Observer'        => [
            'registry',
            'entity'
        ],
        'Potoky_AlertAnonymous_Model_Price'           => [
            'registry'
        ],
        'Potoky_AlertAnonymous_Block_Product_View'    => [
            'allow',
            'login'
        ]
    ];
    private static $helperClasses = [];

    public function setHelpers($controller)
    {
        self::$helpers['data'] = $this;
        self::$helpers['allow'] = Mage::helper('alertanonymous/allow');
        self::$helpers['login'] = Mage::helper('alertanonymous/login');
        self::$helpers['registry'] = Mage::helper('alertanonymous/registry');
        self::$helpers['data_1'] = Mage::helper('core');
        self::$helpers['data_2'] = Mage::helper('anonymouscustomer');
        self::$helpers['entity'] = Mage::helper('anonymouscustomer/entity');

        $controller::$helpers = self::$helpers;
        Potoky_AlertAnonymous_Model_Email_Template::$helpers = self::$helpers;
        Potoky_AlertAnonymous_Model_Customer::$helpers = self::$helpers;
        Potoky_AlertAnonymous_Model_Email::$helpers = self::$helpers;
        Potoky_AlertAnonymous_Model_Observer::$helpers = self::$helpers;
        Potoky_AlertAnonymous_Model_Price::$helpers = self::$helpers;
        Potoky_AlertAnonymous_Block_Product_View::$helpers = self::$helpers;
    }

    public function setUpHelpers($classInstance)
    {
        $className = get_class($classInstance);
        foreach ($this->classHelperMapping[$className] as $method) {

        }
    }
}