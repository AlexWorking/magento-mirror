<?php

class Potoky_AlertAnonymous_Helper_Allow extends Mage_Core_Helper_Abstract
{
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