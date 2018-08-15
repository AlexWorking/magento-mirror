<?php

require_once(
    Mage::getModuleDir('controllers','Mage_Widget') .
    DS . 'Adminhtml' .
    DS . 'Widget' .
    DS . 'InstanceController.php');
class Potoky_ItemBanner_Adminhtml_Widget_InstanceController extends Mage_Widget_Adminhtml_Widget_InstanceController
{
    public function saveAction()
    {
        if($post_data = $this->getRequest()->getPost());
        try {
            if ((bool) $post_data['parmeters']['image']['delete'] == 1) {
                $post_data['parmeters']['image'] = '';
            } else {
                unset($post_data['parmeters']['image']);
                if (isset($_FILES)) {
                    if ($_FILES['parameters']['name']) {
                        $path = Mage::getBaseDir('media') . DS . 'itembanner' . DS;
                        $uploader = new Varien_File_Uploader('parameters[image]');
                        $uploader->setAllowedExtensions(array('jpg', 'png', 'gif'));
                        $uploader->setAllowRenameFiles(true);
                        $uploader->setFilesDispersion(false);
                        $destFile = $path . $_FILES['parameters']['name']['image'];
                        $filename = $uploader->getNewFileName($destFile);
                        $uploader->save($path, $filename);

                        $post_data['parmeters']['image'] = $_FILES['parameters']['name'];
                    }
                }
            }
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            return;
        }

        parent::saveAction();


    }
}