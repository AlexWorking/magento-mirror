<?php

class Potoky_AlertAnonymous_Block_Email_Price extends Mage_ProductAlert_Block_Email_Price
{
    private $unsubscribeHash = 'nohash';

    public function setUnsubscribeHash($hash)
    {
        $this->unsubscribeHash = $hash;
    }

    /**
     * Get store url params
     *
     * @return string
     */
    protected function _getUrlParams()
    {
        $parent = parent::_getUrlParams();
        $parent['anonymous'] = $this->unsubscribeHash;

        return $parent;
    }
}