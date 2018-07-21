<?php

class Potoky_AlertAnonymous_Model_Observer extends Mage_ProductAlert_Model_Observer
{
    const ALERT_SAVE_SUCCESS_MESSAGE = 'The alert subscription has been saved.';
    const ALERT_SAVE_FAILURE_MESSAGE = 'Unable to update the alert subscription.';

    public static $helpers = [];
    public static $alertTypes = ['price'];
    private $rewriteMessage;

    public function __construct(){
        if (empty(self::$helpers)) {
            Mage::helper('alertanonymous')->setUpHelpers($this);
        }
    }

    public function process()
    {
        $parent = parent::process();

        static $timesDone = 1;
        if ($timesDone < 2) {
           self::$helpers['registry']->setRegistry('cron', null, false);
            $timesDone++;
            $this->process();
        }

        return $parent;
    }

    public function avoidDuplication(Varien_Event_Observer $observer)
    {
        if (self::$helpers['registry'] == 'add') {
            $alert = $observer->getEvent()->getObject();
            $data = $this->extractAlertRelatedData('price', clone $alert);
            if($data['status'] === "0" && $alert->getPrice() == $data['price']) {
                $this->rewriteMessage = Mage::helper('productalert')->__('You are already subscribed for this Price alert.');
            }
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
        $data['email'] = self::$helpers['registry']->getRegistry('customer_entity')->getEmail();
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
        if (self::$helpers['registry']->getRegistry('parent_construct') === false) {
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
        if (self::$helpers['registry']->getRegistry('parent_construct') === false) {
            return;
        }

        $data = [];
        $customerId = self::$helpers['registry']->getRegistry('customer_entity')->getId();
        $customer = Mage::getModel('customer/customer')->load($customerId);
        $data['email'] = $customer->getEmail();
        $data['website_id'] = $customer->getWebsiteId();
        $this->processDelete($data, 'priceAll');   
    }

    private function processDelete($data, $actionName)
    {
        $anonymousCustomer = self::$helpers['entity']->getCustomerEntityByRequest(
                'anonymouscustomer/anonymous',
                $data['email'],
                $data['website_id']
        );
        if ($id = $anonymousCustomer->getId()) {
            self::$helpers['registry']->setRegistry(null, $anonymousCustomer, false);
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
        $customer = $observer->getEvent()->getCustomer();
        $email = $customer->getEmail();
        $websiteId = $customer->getWebsiteId();
        $anonymousCustomer = self::$helpers['entity']
            ->getCustomerEntityByRequest('anonymouscustomer/anonymous', $email, $websiteId);
        foreach (self::$alertTypes as $alertype) {
            self::$helpers['registry']->setRegistry(null, $anonymousCustomer, false);
            $collection = Mage::getModel('alertanonymous/' . $alertype)
                ->getCollection()
                ->addFieldToFilter('customer_id', $anonymousCustomer->getId())
                ->addFieldToFilter('website_id', $websiteId);
            foreach ($collection as $anonymousAlert) {
                self::$helpers['registry']->setRegistry();
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
