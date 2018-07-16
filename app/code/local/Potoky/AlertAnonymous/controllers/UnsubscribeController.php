<?php

require_once(
    Mage::getModuleDir('controllers','Mage_ProductAlert').
    DS.'UnsubscribeController.php');
class Potoky_AlertAnonymous_UnsubscribeController extends Mage_ProductAlert_UnsubscribeController
{
    public function preDispatch()
    {
        $unsubscribeHash = $this->getRequest()->getParam('anonymous');
        if (!$unsubscribeHash || $unsubscribeHash == 'nohash') {
            parent::preDispatch();
        }

        Mage_Core_Controller_Front_Action::preDispatch();

        $unsubscribeHash = explode(
            ' ',
            Mage::helper('core')->decrypt($unsubscribeHash)
        );

        $email = $unsubscribeHash[0];
        $websiteId = $unsubscribeHash[1];
        Mage::unregister('potoky_alertanonymous');

        $anonymousCustomer = Mage::helper('anonymouscustomer/entity')
            ->getCustomerEntityByRequest('anonymouscustomer/anonymous', $email, $websiteId);
        if ($id = $anonymousCustomer->getId()) {
            Mage::register('potoky_alertanonymous',
                [
                    'id' => $id,
                    'parent_construct' => false
                ]
            );
        }
    }
}