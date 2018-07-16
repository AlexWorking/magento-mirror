<?php

class Potoky_AlertAnonymous_Model_Observer extends Mage_ProductAlert_Model_Observer
{
    const ALERT_SAVE_SUCCESS_MESSAGE = 'The alert subscription has been saved.';
    const ALERT_SAVE_FAILURE_MESSAGE = 'You are already subscribe for this Price alert.';

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

    public function cascadeDeleteAnonymousAlert(Varien_Event_Observer $observer)
    {
        $test = $observer->getEvent()->getObject();
    }
}