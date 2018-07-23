<?php

class Potoky_AlertAnonymous_Helper_Data extends Mage_Core_Helper_Abstract
{
    public static $helpers = [];

    private $classHelperMapping = [
        'Potoky_AlertAnonymous_AddController'         => [
            'allow'    => 'alertanonymous/allow',
            'login'    => 'alertanonymous/login',
            'entity'   => 'anonymouscustomer/entity',
            'registry' => 'alertanonymous/registry',
            'data'     => 'alertanonymous'
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
            'entity'   => 'anonymouscustomer/entity',
            'data'     => 'alertanonymous',
            'data_2'     => 'productalert',
        ],
        'Potoky_AlertAnonymous_Model_Price'           => [
            'registry' => 'alertanonymous/registry'
        ],
        'Potoky_AlertAnonymous_Model_Stock'           => [
            'registry' => 'alertanonymous/registry'
        ],
        'Potoky_AlertAnonymous_Block_Product_View'    => [
            'allow'    => 'alertanonymous/allow',
            'login'    => 'alertanonymous/login'
        ]
    ];


    public function setUpHelpers($classInstance)
    {
        $className = get_class($classInstance);
        foreach ($this->classHelperMapping[$className] as $helperName => $rout) {
            if(!isset(self::$helpers[$helperName])) {
                self::$helpers[$helperName] = Mage::helper($rout);
            }
            $classInstance::$helpers[$helperName] = & self::$helpers[$helperName];
        }
    }
}