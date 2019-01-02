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
        if ($currentWidgetInstance &&
            $currentWidgetInstance->getType() == 'itembanner/banner') {
            if ($parent['image']['delete']) {
                //TODO disable widget
                $parent['image'] = null;
                $parent['rel_coords_grid'] = Mage::helper('core')->jsonEncode([]);
                $parent['rel_coords_list'] = Mage::helper('core')->jsonEncode([]);
            }
            elseif ($uploaded = $this->imageUpload()) {
                $parent['image'] = $uploaded;
                $parent['rel_coords_grid'] = Mage::helper('core')->jsonEncode([]);
                $parent['rel_coords_list'] = Mage::helper('core')->jsonEncode([]);
            }
            elseif ($image = $currentWidgetInstance->getWidgetParameters()['image']) {
                $parent['image'] = $image;
                $this->manageRelCoords($image, $parent['rel_coords_grid'], 'grid');
                $this->manageRelCoords($image, $parent['rel_coords_list'], 'list');
            }
        }
        $parent['instance_id'] = $currentWidgetInstance->getId();

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

    private function manageRelCoords($baseImageFile, $relCoords, $mode)
    {
        $relCoords = Mage::helper('core')->jsonDecode($relCoords);
        extract($relCoords);

        $image =  new Varien_Image(Mage::helper('itembanner')->getImageUri(
            $baseImageFile,
            '',
            false)
        );
        $modeImageFile = Mage::helper('itembanner')->getImageUri($baseImageFile, $mode, false);
        $image->crop(
            $relCoords[1] * $image->getOriginalHeight(),
            $relCoords[0] * $image->getOriginalWidth(),
            (1 - $relCoords[2]) * $image->getOriginalWidth(),
            (1 - $relCoords[3]) * $image->getOriginalHeight()
        );
        $image->save($modeImageFile);
    }
}