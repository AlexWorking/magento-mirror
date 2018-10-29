<?php

class Potoky_ItemBanner_Adminhtml_Widget_CropperController extends Mage_Adminhtml_Controller_Action
{
    public function cropAction()
    {
        $image = new Varien_Image(Mage::getBaseDir('media') . DS . 'itembanner' . DS . 'Small-mario.png');
        $newFilePath = Mage::getBaseDir('media') . DS . 'itembanner' . DS . 'cropped' . DS . 'Small-mario.png';
        extract($this->getRequest()->getPost());
        $image->crop(
            $y1_grid,
            $x1_grid,
            $image->getOriginalWidth() - $x2_grid,
            $image->getOriginalHeight() - $y2_grid
        );
        $image->save($newFilePath);
    }
}