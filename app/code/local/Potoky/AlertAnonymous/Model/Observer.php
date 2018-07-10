<?php

class Potoky_AlertAnonymous_Model_Observer extends Mage_ProductAlert_Model_Observer
{
    public function process()
    {
        static $timesDone = 1;
        $parent = parent::process();
        Mage::unregister('potoky_alertanonymous');
        if ($timesDone < 2) {
            Mage::register('potoky_alertanonymous', true);
            Potoky_AnonymousCustomer_Model_Customer::setCreateAnonymous(true);
            $timesDone++;
            $this->process();
        }
        Potoky_AnonymousCustomer_Model_Customer::setCreateAnonymous(false);

        return $parent;
    }
}