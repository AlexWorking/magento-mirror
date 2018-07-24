<?php

class Potoky_AlertAnonymous_Block_Email_Stock extends Mage_ProductAlert_Block_Email_Stock
{

    /**
     * Hashed unsubscribe information
     *
     * @var string
     */
    private $unsubscribeHash = 'nohash';


    /**
     * Sets hashed unsubscribe info
     *
     * @var $hash
     */
    public function setUnsubscribeHash($hash)
    {
        $this->unsubscribeHash = $hash;
    }

    /**
     * Get store url params after adding to them hashed unsubscribe info
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