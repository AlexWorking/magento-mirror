<?php

class Potoky_AlertAnonymous_Helper_Allow extends Mage_Core_Helper_Abstract
{
    /**
     * Check whether this type of alert is available to subscribe to
     * for anonymous customer too
     *
     * @param null $templateId
     * @return mixed
     */
    public function isCurrentAlertAllowedForAnonymous($templateId = null)
    {
        if($templateId === null) {
            $urlParts = explode('/', Mage::helper('core/url')->getCurrentUrl());
            $alertKey = array_search('add', $urlParts) + 1;
            $templateId = $urlParts[$alertKey];
        }

        return Mage::getStoreConfig(sprintf('catalog/productalert/allow_%s_anonymous', $templateId));
    }
}