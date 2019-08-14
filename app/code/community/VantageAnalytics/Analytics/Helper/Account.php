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

    public function getExtensionVersion()
    {
        return (string) Mage::getConfig()->getNode()->modules->VantageAnalytics_Analytics->version;
    }

    protected function collectStoreInfo()
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

    public function registerAccount($params)
    {
        $verifyUrl = Mage::helper("analytics/account")->verifyAccountUrl();
        Mage::helper("analytics/log")->logInfo("The account verify URL is ${verifyUrl}");

        $channel = curl_init($verifyUrl);

        curl_setopt($channel, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($channel, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($channel, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($channel, CURLOPT_CONNECTTIMEOUT_MS, 12200);
        curl_setopt($channel, CURLOPT_TIMEOUT_MS, 15000);

        $account = array(
            'username' => $params['username'],
            'secret' => $params['secret'],
            'stores' => $this->collectStoreInfo()
        );

        $body = json_encode($account);
        curl_setopt($channel, CURLOPT_POSTFIELDS, $body);
        $headers = array(
                'Content-type: application/json',
                'Content-length: ' . strlen($body)
            );


        curl_setopt($channel, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($channel);

        $status = curl_getinfo($channel, CURLINFO_HTTP_CODE);
        if ($status >= 500) {
            Mage::throwException("An error occurred. Please try again later.");
        }

        if (curl_errno($channel)) {
             $errorDesc = curl_error($channel);
             curl_close($channel);
             Mage::throwException("An error occurred. Please try again later.");
        }

        curl_close($channel);
        $response = json_decode($result, true);

        return $response;
    }
}
