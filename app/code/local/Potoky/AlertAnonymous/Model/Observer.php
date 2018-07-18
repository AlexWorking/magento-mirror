<?php

class Potoky_AlertAnonymous_Model_Observer extends Mage_ProductAlert_Model_Observer
{
    const ALERT_SAVE_SUCCESS_MESSAGE = 'The alert subscription has been saved.';
    const ALERT_SAVE_FAILURE_MESSAGE = 'Unable to update the alert subscription.';


    public static $alertTypes = ['price'];
    private $rewriteMessage;

    public function process()
    {
        $parent = parent::process();

        static $timesDone = 1;
        Mage::unregister('potoky_alertanonymous');
        if ($timesDone < 2) {
            Mage::register('potoky_alertanonymous', [
                'id' => 'anonymous_email',
                'parent_construct' => false]
            );
            $timesDone++;
            $this->process();
        }

        return $parent;
    }

    public function avoidDuplication(Varien_Event_Observer $observer)
    {
        $alert = $observer->getEvent()->getObject();
        $data = $this->extractNecessaryFields('price', clone $alert);
        if($data['status'] === "0" && $alert->getPrice() == $data['price']) {
            $this->rewriteMessage = Mage::helper('productalert')->__('You are already subscribed for this Price alert.');
        }

        return $this;
    }

    /*
     * To be REDONE
     */
    private function extractNecessaryFields($alertType, $alert)
    {
        $alert->loadByParam();
        $data['price'] = $alert->getData($alertType);
        $data['status'] = $alert->getData('status');

        return $data;
    }

    public function rewriteMessage(Varien_Event_Observer $observer)
    {
        if (isset($this->rewriteMessage)) {
            $messages = Mage::getSingleton('catalog/session')->getMessages();
            $lastAddedMessage =$messages->getLastAddedMessage();
            $code = $lastAddedMessage->getCode();
            if($code == self::ALERT_SAVE_SUCCESS_MESSAGE ||
                $code == self::ALERT_SAVE_FAILURE_MESSAGE) {
                $lastAddedMessage->setCode($this->rewriteMessage);
            }
        }

        return $this;
    }

    public function cascadeDelete(Varien_Event_Observer $observer)
    {
        if (!$controller = Mage::registry('potoky_alertanonymous')['controller']) {
            return;
        }
        Mage::unregister('potoky_alertanonymous');
        $anonymousCustomer = Mage::helper('anonymouscustomer/entity')
            ->getCustomerEntityByRequest(
                'anonymouscustomer/anonymous',
                $controller->getCustomerIdentifiers()[0],
                $controller->getCustomerIdentifiers()[1]
            );
        if ($id = $anonymousCustomer->getId()) {
            Mage::register('potoky_alertanonymous',
                [
                    'id' => $id,
                    'parent_construct' => false
                ]
            );

            $actionName = explode('_', $observer->getEvent()->getName())[0] . 'action';
            try{
                $controller->$actionName();
            } catch (Exception $e) {
                return;
            }
        }
    }

    public function copyAlertsToCoreTables(Varien_Event_Observer $observer)
    {
        if (Mage::getStoreConfig('customer/cascade_delete/when' == 'created')) {
            return;
        }
        $customer = $observer->getEvent()->getCustomer();
        $email = $customer->getEmail();
        $websiteId = $customer->getWebsiteId();
        $anonymousCustomerId = Mage::helper('anonymouscustomer/entity')
            ->getCustomerEntityByRequest('anonymouscustomer/anonymous', $email, $websiteId)
            ->getId();
        Mage::unregister('potoky_alertanonymous');
        foreach (self::$alertTypes as $alertype) {
            Mage::register('potoky_alertanonymous',
                [
                    'id' => $anonymousCustomerId,
                    'parent_construct' => false,
                ]
            );
            $collection = Mage::getModel('alertanonymous/' . $alertype)
                ->getCollection()
                ->addFieldToFilter('customer_id', $anonymousCustomerId)
                ->addFieldToFilter('website_id', $websiteId);
            foreach ($collection as $alert) {
                Mage::unregister('potoky_alertanonymous');
                $coreAlert = Mage::getModel('productalert/' . $alertype);
                $coreAlert->setData([
                    'customer_id' => $customer->getId(),
                    'product_id'  => $alert->getProductId(),
                    'website_id'  => $alert->getWebsiteId(),
                    'add_date'    => $alert->getAddDate(),
                    'send_date'   => $alert->getSendDate(),
                    'status'      => $alert->getStatus(),
                ]);
                try{
                    $coreAlert->save();
                } catch(Exception $e) {
                    Mage::logException($e);
                }
            }
        }
    }
}
