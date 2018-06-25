<?php

require_once(
    Mage::getModuleDir('controllers','Mage_ProductAlert').
    DS.'AddController.php');
class Potoky_AlertAnonymous_AddController extends Mage_ProductAlert_AddController
{
    public function preDispatch()
    {
        /*$session = Mage::getSingleton('customer/session');
        if (!$session->isLoggedIn()) {
            $session->authenticate($this, $this->_getRefererUrl());
            $block = $this->loadLayout(false);
        }*/
    }

}