<?php

class Potoky_ItemBanner_Block_Adminhtml_Widget_Cropped extends Mage_Adminhtml_Block_Template
{
    private $imageUrl;

    private $imageSquare;

    protected function _construct()
    {
        parent::_construct();

        $this->imageUrl = $this->prepareImageUrl();
        $this->imageSquare = $this->measureImageSquare();
        if (Mage::helper('itembanner')->getCurrentInstance()->getWidgetParameters()['image']) {
            $this->setTemplate('itembanner/cropped.phtml');
        } else {
            $this->setTemplate('');
        }
    }

    private function prepareImageUrl() {
        return Mage::helper('itembanner')->getImageUri(
            Mage::registry('current_widget_instance')->getWidgetParameters()['image']
        );
    }

    private function measureImageSquare()
    {
        $imageData = getimagesize($this->imageUrl);

        return $imageData[0] * $imageData[1];
    }

    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    public function getImageSquare()
    {
        return $this->imageSquare;
    }
}