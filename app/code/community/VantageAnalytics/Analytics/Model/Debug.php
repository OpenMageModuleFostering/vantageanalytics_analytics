<?php

class VantageAnalytics_Analytics_Model_Debug
{

    public static function factory()
    {
        return new self();
    }

    // Makes it simpler to accept the
    // debug information in the inbound-api
    public function storeIds()
    {
        return array_values(Mage::app()->getWebsite()->getStoreIds());
    }

    public function entityType()
    {
        return 'heartbeat';
    }

    public function php()
    {
        return phpversion();
    }

    public function magentoVersion()
    {
        $version = Mage::getVersion();
        $edition = Mage::getEdition();

        return $version . "/" . $edition;
    }

    public function machine()
    {
        return php_uname();
    }

    public function installDate()
    {
        $config = Mage::app()->getConfig();

        return VantageAnalytics_Analytics_Helper_DateFormatter::factory(
            $config->getNode('global/install/date')
        )->toDate();
    }

    public function stores()
    {
        $stores = array();

        foreach (Mage::app()->getStores() as $store) {
            $stores[$store->getId()] = $store->getCode();
        }

        return $stores;
    }

    public function websites()
    {
        $websites = array();

        $sites = Mage::app()->getWebsites(true);

        foreach ($sites as $website) {
            $websites[$website->getId()] = $website->getCode();
        }


        return $websites;
    }

    public function extensions()
    {
        return VantageAnalytics_Analytics_Helper_Extension_Lister::factory()->extensions();
    }

    public function verified()
    {
        return Mage::helper('analytics/account')->isVerified();
    }

    public function extensionVersion()
    {
        return Mage::helper('analytics/account')->getExtensionVersion();
    }

    public function toVantage()
    {
        $methods = array_diff(
            get_class_methods($this),
            array('toVantage', '__construct', 'factory')
        );

        $data = array();

        foreach ($methods as $method) {
            $attr = strtolower(preg_replace(
               '/(?<=\\w)(?=[A-Z])/',"_$1", $method));
            $data[$attr] = call_user_func(array($this, $method));
        }

        return $data;
    }

}
