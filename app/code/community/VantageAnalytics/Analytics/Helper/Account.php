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

    public function verifyAccountUrl()
    {
        return dirname($this->vantageUrl()) . '/verify';
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

    public function isCronEnabled()
    {
        return Mage::getStoreConfig('vantageanalytics/export/cron', Mage::app()->getStore());
    }

    public function setCronEnabled($enabled)
    {
        Mage::getConfig()->saveConfig('vantageanalytics/export/cron', $enabled);
        Mage::getConfig()->reinit();
    }

    public function getExtensionVersion()
    {
        return (string) Mage::getConfig()->getNode()->modules->VantageAnalytics_Analytics->version;
    }

    public function collectStoreInfo()
    {
        $stores = array();

        foreach (Mage::app()->getWebsites() as $store) {
            $stores[] = array(
                'store_id' => $store->getId(),
                'domain' => $store->getDefaultStore()->getBaseUrl(),
                'name' => $store->getName()
            );
        }

        return $stores;
    }

    public function hashEquals($safe, $user) {
        $safeLen = strlen($safe);
        $userLen = strlen($user);

        if ($safeLen == 0) {
            return false;
        }

        if ($userLen != $safeLen) {
            return false;
        }

        $result = 0;

        for ($i = 0; $i < $userLen; $i++) {
            $result |= (ord($safe[$i]) ^ ord($user[$i]));
        }

        // They are only identical strings if $result is exactly 0...
        return $result === 0;
    }

    public function verifySecret($providedSecret)
    {
        $mySecret = $this->secret();
        return $this->hashEquals($mySecret, $providedSecret);
    }


    public function getSiteId()
    {
        $siteId = Mage::getStoreConfig('vantageanalytics/accountoptions/siteid', 0);

        if (empty($siteId)) {
            $siteId = uniqid();
            Mage::getConfig()->saveConfig('vantageanalytics/accountoptions/siteid', $siteId);
            Mage::getConfig()->reinit();
        }

        return $siteId;
    }
}
