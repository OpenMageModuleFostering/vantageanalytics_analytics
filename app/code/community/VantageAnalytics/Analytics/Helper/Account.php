<?php

class VantageAnalytics_Analytics_Helper_Account extends Mage_Core_Helper_Abstract
{
    public function username()
    {
        return Mage::getStoreConfig('vantageanalytics/accountoptions/accountid', Mage::app()->getStore());
    }

    public function setUsername($username)
    {
        Mage::getConfig()->saveConfig('vantageanalytics/accountoptions/accountid', $username);
        Mage::getConfig()->reinit();
    }

    public function secret()
    {
        return Mage::getStoreConfig('vantageanalytics/accountoptions/accountpwd', Mage::app()->getStore());
    }

    public function setSecret($secret)
    {
        Mage::getConfig()->saveConfig('vantageanalytics/accountoptions/accountpwd', $secret);
        Mage::getConfig()->reinit();
    }

    public function vantageUrl()
    {
        return dirname(Mage::getStoreConfig('vantageanalytics/accountoptions/vantageurl', Mage::app()->getStore())) . '/webhook';
    }

    public function registerAccountUrl()
    {
        return dirname($this->vantageUrl()) . '/register';
    }

    public function notifyVantageUrl()
    {
        return dirname($this->vantageUrl()) . '/notify';
    }

    public function accountInfoUrl()
    {
        return dirname($this->vantageUrl()) . '/info';
    }

    public function accountPixelUrl()
    {
        return $this->appUrl() . 'ecom/pixel';
    }

    public function appUrl()
    {
        return Mage::getStoreConfig('vantageanalytics/accountoptions/app_url', Mage::app()->getStore());
    }

    public function setAppUrl($url)
    {
        Mage::getConfig()->saveConfig('vantageanalytics/accountoptions/app_url', $url);
        Mage::getConfig()->reinit();
    }

    public function setVantageUrl($url)
    {
        Mage::getConfig()->saveConfig('vantageanalytics/accountoptions/vantageurl', $url);
        Mage::getConfig()->reinit();
    }

    public function isVerified()
    {
        return Mage::getStoreConfig('vantageanalytics/accountoptions/verified', Mage::app()->getStore());
    }

    public function setIsVerified($isVerified)
    {
        Mage::getConfig()->saveConfig('vantageanalytics/accountoptions/verified', $isVerified);
        Mage::getConfig()->reinit();
    }

    public function isExportDone()
    {
        return Mage::getStoreConfig('vantageanalytics/export/done', Mage::app()->getStore());
    }

    public function setExportDone($done)
    {
        Mage::getConfig()->saveConfig('vantageanalytics/export/done', $done);
        Mage::getConfig()->reinit();
    }

    public function getExtensionVersion()
    {
        return (string) Mage::getConfig()->getNode()->modules->VantageAnalytics_Analytics->version;
    }
}
