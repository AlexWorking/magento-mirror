<?php

/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 4/19/2018
 * Time: 10:56 AM
 */
class Potoky_ImageAutoImport_Adminhtml_ImageAutoImportController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
        Mage::getSingleton('adminhtml/session')->unsetData('resultMessage');
    }

    public function validateAction()
    {
        try {
            $import = Mage::getModel('importexport/import');
            $sourceFile = $import->setData('entity', 'import_to_queue')->uploadSource();
            $imageInfo = Mage::getModel('imageautoimport/imageinfo');
            $imageInfo->setAdapter($sourceFile)
                ->validateContent()
                ->validateColNames()
                ->validateRows();
            $key = 0;
            $rows = $imageInfo->getRows();
            do {
                $imageInfo->setData([
                    'product_sku' => $rows[$key]['sku'],
                    'image_url' => $rows[$key]['url'],
                    'image_size' => filesize($rows[$key]['url']),
                    'status' => 'In Queue',
                    //'loading_at' => strftime('%Y-%m-%d %H:%M:%S', time())
                ]);
                $imageInfo->save();
            } while (isset($rows[++$key]));
            Mage::getSingleton('adminhtml/session')->setData(
                'resultMessage',
                Mage::Helper('imageautoimport')->__('Images were successfully added to queue!'));
            //Mage::dispatchEvent('images_imported', ['success' => 1]);
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->setData('resultMessage', $e->getMessage());
        }
        $this->_redirect('*/*/index');
    }

    public function flowAction()
    {
        preg_match(
            '#(.+\/id\/)([0-9]+)(\/.+)#',
            $this->_getRefererUrl(),
            $matches
        );
        $id = $matches[2];
        $currentProduct = Mage::getModel('catalog/product')->load($id);
        Mage::register('current_product', $currentProduct);
        $this->loadLayout();
        $this->renderLayout();
    }

    public function flowGridAction()
    {
        preg_match(
            '#(.+\/id\/)([0-9]+)(\/.+)#',
            $this->_getRefererUrl(),
            $matches
        );
        $id = $matches[2];
        $currentProduct = Mage::getModel('catalog/product')->load($id);
        Mage::register('current_product', $currentProduct);
        $this->loadLayout();
        $this->renderLayout();
    }
}
