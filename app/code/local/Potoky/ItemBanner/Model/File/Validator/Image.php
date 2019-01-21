<?php

class Potoky_ItemBanner_Model_File_Validator_Image extends Mage_Core_Model_File_Validator_Image
{
    public function validate($filePath)
    {
        $image = new Varien_Image($filePath);

        if ($image->getOriginalWidth() < 800 || $image->getOriginalHeight() < 800) {
            Mage::throwException(
                Mage::helper('itembanner')
                ->__('At least one original size of the image is less than 800 px. The Image has not been saved'),
                'adminhtml/session'
            );
        }

        return parent::validate($filePath);
    }
}