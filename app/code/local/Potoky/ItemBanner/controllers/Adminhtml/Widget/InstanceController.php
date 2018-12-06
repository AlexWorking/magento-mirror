<?php

require_once(
    Mage::getModuleDir('controllers','Mage_Widget') .
    DS . 'Adminhtml' .
    DS . 'Widget' .
    DS . 'InstanceController.php');
class Potoky_ItemBanner_Adminhtml_Widget_InstanceController extends Mage_Widget_Adminhtml_Widget_InstanceController
{
    /**
     * Prepare widget parameters
     *
     * @return array
     */
    protected function _prepareParameters()
    {
        $parent = parent::_prepareParameters();

        $currentWidgetInstance = Mage::registry('current_widget_instance');
        if($currentWidgetInstance &&
            $currentWidgetInstance->getType() == 'itembanner/banner') {
            if ($parent['image']['delete']) {
                //TODO disable widget
                $parent['image'] = null;
            }
            elseif ($uploaded = $this->imageUpload()) {
                $parent['image'] = $uploaded;
            }
            elseif ($image = $currentWidgetInstance->getWidgetParameters()['image']) {
                $parent['image'] = $image;
            }

            $parent['instance_id'] = $currentWidgetInstance->getId();
            $parent['rel_coords'] = $this->getRequest()->getPost('rel_coords');
        }

        return $parent;
    }

    private function imageUpload()
    {
        $path = Mage::getBaseDir('media') . DS . 'itembanner' . DS;
        try {
            $uploader = new Mage_Core_Model_File_Uploader('parameters[image]');
            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
            $uploader->setAllowRenameFiles(true);
            $uploader->addValidateCallback(
                Potoky_ItemBanner_Model_File_Validator_Image::NAME,
                new Potoky_ItemBanner_Model_File_Validator_Image(),
                "validate"
            );
            $result = $uploader->save($path);

        } catch (Exception $e) {
            if ($e->getCode() != Mage_Core_Model_File_Uploader::TMP_NAME_EMPTY) {
                Mage::logException($e);
            }

            return false;
        }

        return $result['file'];
    }

    private function crop($src, $x1, $y1, $x2, $y2, $w, $h, $mode)
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
}