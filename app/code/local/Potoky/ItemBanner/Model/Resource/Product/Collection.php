<?php

class Potoky_ItemBanner_Model_Resource_Product_Collection extends Mage_Catalog_Model_Resource_Product_Collection
{
    private $_collectionSizeWithBannersWithBanners;

    public function getSize()
    {
        if (isset($this->_collectionSizeWithBanners)) {
            return $this->_collectionSizeWithBanners;
        }

        $parent = parent::getSize();

        if($data = Mage::registry('potoky_itembanner')) {
            $parent += $data['count'];
            $this->_collectionSizeWithBanners = $parent;
        }

        return $parent;
    }

    public function _loadEntities($printQuery = false, $logQuery = false)
    {
        if(!$data = Mage::registry('potoky_itembanner')) {

            return parent::_loadEntities($printQuery, $logQuery);
        }

        $prevPageElementNumber = ($this->getCurPage() - 1) * $this->getPageSize();
        $prevPageProdNumber = $prevPageElementNumber - $data['previousPagesBannerQty'];

        if ($this->_pageSize) {
            $this->getSelect()->limit( $this->_pageSize, $prevPageProdNumber);
        }

        $this->printLogQuery($printQuery, $logQuery);

        try {
            /**
             * Prepare select query
             * @var string $query
             */
            $query = $this->_prepareSelect($this->getSelect());
            $rows = $this->_fetchAll($query);
        } catch (Exception $e) {
            Mage::printException($e, $query);
            $this->printLogQuery(true, true, $query);
            throw $e;
        }

        foreach ($rows as $v) {
            $object = $this->getNewEmptyItem()
                ->setData($v);
            $this->addItem($object);
            if (isset($this->_itemsById[$object->getId()])) {
                $this->_itemsById[$object->getId()][] = $object;
            } else {
                $this->_itemsById[$object->getId()] = array($object);
            }
        }

        return $this;
    }
}