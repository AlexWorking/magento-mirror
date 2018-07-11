<?php

class Potoky_AlertAnonymous_Model_Observer extends Mage_ProductAlert_Model_Observer
{
    public function process()
    {
        $parent = parent::process();

        static $timesDone = 1;
        Mage::unregister('potoky_alertanonymous');
        if ($timesDone < 2) {
            Mage::register('potoky_alertanonymous', ['parent_construct' => false]);
            $timesDone++;
            $this->process();
        }

        return $parent;
    }
}