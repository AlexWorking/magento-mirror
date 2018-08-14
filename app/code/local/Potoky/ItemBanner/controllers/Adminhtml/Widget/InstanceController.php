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
            if ((bool) $post_data['image']['delete'] == 1) {
                $post_data['iconimage'] = '';
            } else {
                unset($post_data['image']);
                if (isset($_FILES)) {
                    if ($_FILES['parameters[image]']['name']) {
                        if ($this->getRequest()->getParam("id")) {
                            $model = Mage::getModel("service/service")->load($this->getRequest()->getParam("id"));
                            if ($model->getData('iconimage')) {
                                $io = new Varien_Io_File();
                                $io->rm(Mage::getBaseDir('media') . DS . implode(DS, explode('/', $model->getData('iconimage'))));
                            }
                        }
                        $path = Mage::getBaseDir('media') . DS . 'service' . DS . 'service' . DS;
                        $uploader = new Varien_File_Uploader('iconimage');
                        $uploader->setAllowedExtensions(array('jpg', 'png', 'gif'));
                        $uploader->setAllowRenameFiles(true);
                        $uploader->setFilesDispersion(false);
                        $destFile = $path . $_FILES['iconimage']['name'];
                        $filename = $uploader->getNewFileName($destFile);
                        $uploader->save($path, $filename);

                        $post_data['iconimage'] = 'service/service/' . $filename;
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