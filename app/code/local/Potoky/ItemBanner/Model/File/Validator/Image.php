<?php

class Potoky_ItemBanner_Model_File_Validator_Image extends Mage_Core_Model_File_Validator_Image
{
    public function validate($filePath)
    {
        $image = new Varien_Image($filePath);

        if ($image->getOriginalWidth() < 800 || $image->getOriginalHeight() < 800) {
            Mage::unregister('potoky_itembanner_validation');
            Mage::register('potoky_itembanner_validation', false);

            return false;
        }

        return parent::validate($filePath);
    }
}