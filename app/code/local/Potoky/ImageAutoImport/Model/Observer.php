<?php

class Potoky_ImageAutoImport_Model_Observer
{
    public function importImages(Varien_Event_Observer $observer)
    {
        $importData = Mage::getModel('imageautoimport/imageinfo')
            ->getCollection()
            ->addFieldToFilter('status', array('in' => array('In Queue', 'Retrial')))
            ->setOrder('loading_at', 'ASC');
        foreach ($importData as $row) {

            if ($row['status'] == 'Retrial' && $row['loading_at'] + 86400 < time()) {
                continue;
            }
            $header = get_headers($row['image_url'])[0];
            $statusPattern = '~(HTTP/[0-9.]+ )([0-9]+)( .+)~';
            $matches =[];
            preg_match($statusPattern, $header, $matches);
            switch ($matches[2]) {
                case '200':
                    try {
                        $errorMessage = $this->coreProcess($row['product_sku'], $row['image_url']);
                        $row->setData([
                            'record_id'     => $row['record_id'],
                            'loading_at'    => strftime('%Y-%m-%d %H:%M:%S', time()),
                            'status'        => ($errorMessage === '') ? 'Loaded' : 'Error',
                            'error_message' => ($errorMessage === '') ? null : $errorMessage
                        ]);
                        $row->save();
                    } catch (Exception $e) {
                        $row->setData([
                            'record_id'     => $row['record_id'],
                            'loading_at'    => strftime('%Y-%m-%d %H:%M:%S', time()),
                            'status'        => 'Loaded',
                            'error_message' => $e->getMessage()
                        ]);
                        $row->save();
                    }
                    break;
                case '404':
                    $row->setData([
                        'record_id'     => $row['record_id'],
                        'loading_at'    => strftime('%Y-%m-%d %H:%M:%S', time()),
                        'status'        => 'Retrial',
                        'error_message' => $header
                    ]);
                    $row->save();
                    break;
                default:
                    $row->setData([
                        'record_id'     => $row['record_id'],
                        'loading_at'    => strftime('%Y-%m-%d %H:%M:%S', time()),
                        'status'        => 'Error',
                        'error_message' => $header
                    ]);
                    $row->save();
                    break;
            }

        }
    }

    private function coreProcess($productSku, $imageUrl)
    {
        $errorMessage = '';
        $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $productSku);
        try {
            if (!$product) {
                throw new Exception(sprintf(
                    Mage::Helper('imageautoimport')->__("Product with sku %s does not exist anymore"),
                    $productSku
                    ), 1000001
                );
            }

            $imageTypes = null;
            if ($product->getImage() === null) {
                $imageTypes[] = 'image';
            }
            if ($product->getSmallImage() === null) {
                $imageTypes[] = 'small_image';
            }
            if ($product->getThumbnail() === null) {
                $imageTypes[] = 'thumbnail';
            }

            $product->addImageToMediaGallery($this->downloadImage($imageUrl), $imageTypes, false, false);
            $product->save();
        } catch (Exception $e) {
            $errorMessage = $e->getMessage() . ' ' . $e->getCode();
        }

        return $errorMessage;
    }

    private function downloadImage($imageUrl)
    {
        $content = file_get_contents($imageUrl);
        $lastSeparator = max(
            strripos($imageUrl, '/'),
            strripos($imageUrl, '\\')
        );
        $fileName = 'media/autoimport/' . substr($imageUrl, $lastSeparator + 1);
        file_put_contents($fileName, $content);

        return $fileName;
    }
}
