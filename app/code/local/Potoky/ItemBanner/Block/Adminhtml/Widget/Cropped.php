<?php

class Potoky_ItemBanner_Block_Adminhtml_Widget_Cropped extends Mage_Adminhtml_Block_Template
{
    private $imageUrl;

    private $origDimensions = [];

    protected function _construct()
    {
        parent::_construct();

        $this->imageUrl = $this->prepareImageUrl();
        $this->origDimensions = $this->measureImage();
    }

    private function prepareImageUrl() {
        return Mage::helper('itembanner')->getImageUrl(
            Mage::registry('current_widget_instance')->getWidgetParameters()['image']
        );
    }

    private function measureImage() {
        $imageData = getimagesize($this->imageUrl);

        return [
            'width'  => $imageData[0],
            'height' => $imageData[1]
        ];
    }

    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    public function getOrigDimension($dimension)
    {
        return $this->origDimensions[$dimension];
    }
}