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
                'context' => 'cron',
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
        $data = $this->extractAlertRelatedData('price', clone $alert);
        if($data['status'] === "0" && $alert->getPrice() == $data['price']) {
            $this->rewriteMessage = Mage::helper('productalert')->__('You are already subscribed for this Price alert.');
        }

        return $this;
    }

    /*
     * To be REDONE (?)
     */
    private function extractAlertRelatedData($alertType, $alert)
    {
        $data = [];
        if (!$alert->getId()) {
            $alert->loadByParam();
        }
        $data['customer_id'] = $alert->getCustomerId();
        $data['website_id'] = $alert->getWebsiteId();
        $data['product_id'] = $alert->getProductId();
        $data['email'] = Mage::getModel('customer/customer')
            ->load($data['customer_id'])
            ->getEmail();
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

    public function cascadeDeletePrice(Varien_Event_Observer $observer)
    {
        if (Mage::registry('potoky_alertanonymous')['parent_construct'] === false) {
            return;
        }

        $alert = $observer->getEvent()->getObject();
        $this->processDelete(
            $this->extractAlertRelatedData('price', $alert),
            'price'
        );
    }

    public function cascadeDeletePriceAll(Varien_Event_Observer $observer)
    {
        if (Mage::registry('potoky_alertanonymous')['parent_construct'] === false) {
            return;
        }

        $data = [];
        $customerId = Mage::registry('potoky_alertanonymous')['id'];
        $customer = Mage::getModel('customer/customer')->load($customerId);
        $data['email'] = $customer->getEmail();
        $data['website_id'] = $customer->getWebsiteId();
        $this->processDelete($data, 'priceAll');   
    }

    private function processDelete($data, $actionName)
    {
        Mage::unregister('potoky_alertanonymous');
        $anonymousCustomer = Mage::helper('anonymouscustomer/entity')
            ->getCustomerEntityByRequest(
                'anonymouscustomer/anonymous',
                $data['email'],
                $data['website_id']
            );
        if ($id = $anonymousCustomer->getId()) {
            Mage::register('potoky_alertanonymous',
                [
                    'customer_entity' => $anonymousCustomer,
                    'parent_construct' => false
                ]
            );
            $modelName = (strstr($actionName, 'All') !== 'All') ? $actionName : strstr($actionName, 'All', true);
            if ($modelName === $actionName) {
                $anonymousAlert = $model  = Mage::getModel('productalert/' . $modelName)
                    ->setCustomerId($id)
                    ->setProductId($data['product_id'])
                    ->setWebsiteId($data['website_id'])
                    ->loadByParam();
                $anonymousAlert->delete();
            } else {
                Mage::getModel('productalert/' . $modelName)->deleteCustomer(
                    $id,
                    $data['website_id']
                );
            }
        }

        return $this;
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
                    'customer_entity' => $anonymousCustomerId,
                    'parent_construct' => false,
                ]
            );
            $collection = Mage::getModel('alertanonymous/' . $alertype)
                ->getCollection()
                ->addFieldToFilter('customer_id', $anonymousCustomerId)
                ->addFieldToFilter('website_id', $websiteId);
            foreach ($collection as $anonymousAlert) {
                Mage::unregister('potoky_alertanonymous');
                $coreAlert = Mage::getModel('productalert/' . $alertype);
                $coreAlert->setData([
                    'customer_id' => $customer->getId(),
                    'product_id'  => $anonymousAlert->getProductId(),
                    'price'       => $anonymousAlert->getPrice(),
                    'website_id'  => $anonymousAlert->getWebsiteId(),
                    'add_date'    => $anonymousAlert->getAddDate(),
                    'send_date'   => $anonymousAlert->getSendDate(),
                    'status'      => $anonymousAlert->getStatus(),
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
