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
        $currentWidgetInstance = Mage::helper('itembanner')->getCurrentInstance();

        $parent = parent::_prepareParameters();

        if($currentWidgetInstance &&
            $currentWidgetInstance->getType() === 'itembanner/banner') {
            $encodedEmptyArray = Mage::helper('core')->jsonEncode([]);
            $oldRelCoordsGrid = $currentWidgetInstance->getWidgetParameters()['rel_coords_grid'] ?? $encodedEmptyArray;
            $oldRelCoordsList = $currentWidgetInstance->getWidgetParameters()['rel_coords_list'] ?? $encodedEmptyArray;
            function assignRelCoords(&$arr, $value)
            {
                $arr['rel_coords_grid'] = $value;
                $arr['rel_coords_list'] = $value;
            }
            if ($parent['image']['delete']) {
                //TODO disable widget
                $parent['image'] = null;
                assignRelCoords($parent, $encodedEmptyArray);
            }
            elseif ($uploaded = $this->imageUpload()) {
                $parent['image'] = $uploaded;
                assignRelCoords($parent, $encodedEmptyArray);
            }
            elseif ($image = $currentWidgetInstance->getWidgetParameters()['image']) {
                $parent['image'] = $image;
                $parent['rel_coords_grid'] = ($parent['rel_coords_grid']) ? $this->manageRelCoords($image, $parent['rel_coords_grid'], 'grid') : $oldRelCoordsGrid;
                $parent['rel_coords_list'] = ($parent['rel_coords_list']) ? $this->manageRelCoords($image, $parent['rel_coords_list'], 'list') : $oldRelCoordsList;
            } else {
                assignRelCoords($parent, $encodedEmptyArray);
            }

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

        return Mage::helper('core')->jsonEncode($relCoords);
    }
}