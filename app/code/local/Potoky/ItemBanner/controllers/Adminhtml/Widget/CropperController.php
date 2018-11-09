<?php

class Potoky_ItemBanner_Adminhtml_Widget_CropperController extends Mage_Adminhtml_Controller_Action
{
    public function cropAction()
    {
        extract($this->getRequest()->getPost());

        function crop($src, $x1, $y1, $x2, $y2, $w, $h, $mode)
        {
            $file = substr($src, strrpos($src, '/'));
            $image = new Varien_Image(Mage::getBaseDir('media') . DS . 'itembanner' . DS . $file);
            $newFilePath = Mage::getBaseDir('media') . DS . 'itembanner' . DS . $mode . DS . $file;
            $image->crop(
                ($y1 * $image->getOriginalWidth()) / $w,
                ($x1 * $image->getOriginalHeight()) / $h,
                $image->getOriginalWidth() * (1 - $x2 / $w),
                $image->getOriginalHeight() * (1 - $y2 / $h)
            );
            $image->save($newFilePath);
        }

        if ($w_grid != 0 && $h_grid != 0) {
            crop($src_img, $x1_grid, $y1_grid, $x2_grid, $y2_grid, $w_img, $h_img, 'grid');
        }

        if ($w_list != 0 && $h_list != 0) {
            crop($src_img, $x1_list, $y1_list, $x2_list, $y2_list, $w_img, $h_img, 'list');
        }

    }
}