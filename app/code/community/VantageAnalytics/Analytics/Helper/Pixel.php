<?php

class VantageAnalytics_Analytics_Helper_Pixel extends Mage_Core_Helper_Abstract
{
    public function getPixelURL()
    {
        return Mage::getStoreConfig("vantageanalytics/trackingpixel/url");
    }

    public function setPixelURL($url)
    {
        Mage::getConfig()->saveConfig('vantageanalytics/trackingpixel/url', $url);
    }
}
