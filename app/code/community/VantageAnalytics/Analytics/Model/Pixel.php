<?php
class VantageAnalytics_Analytics_Model_Pixel
{
    public function requiresPixelUrl()
    {
        $stores = Mage::app()->getStores();
        foreach ($stores as $store) {
            $configPixelUrl = $store->getConfig('vantageanalytics/trackingpixel/url');
            if (!$configPixelUrl) {
                return true;
            }
        }
        return false;
    }

    public function getStoreOwnerIds()
    {
        $api = new VantageAnalytics_Analytics_Model_Api_Request();
        $infoUrl = Mage::helper("analytics/account")->accountInfoUrl();
        $data = $api->request('POST', $infoUrl, array());
        if (array_key_exists('stores', $data)) {
            return $data['stores'];
        }
        return array();
    }

    public function getPixelUrls($ownerIds)
    {
        $api = new VantageAnalytics_Analytics_Model_Api_Request();
        $pixelUrl = Mage::helper("analytics/account")->accountPixelUrl();
        $data = $api->request('POST', $pixelUrl, array('owner_ids' => $ownerIds));
        if (array_key_exists('pixels', $data)) {
            return $data['pixels'];
        }
        return array();
    }

    public function updateStorePixelUrl($storeId, $pixelUrl)
    {
        $store = Mage::app()->getStore($storeId);
        if ($store) {
            $config = Mage::app()->getConfig();
            $config->saveConfig('vantageanalytics/trackingpixel/url',
                                  $pixelUrl, 'stores', $storeId);
        }
    }

    public function pollPixelUrls()
    {
        if ($this->requiresPixelUrl()) {
            $stores = $this->getStoreOwnerIds();
            $ownerIds = array();
            $storeOwnerIdMap = array();
            foreach ($stores as $store) {
                $ownerIds[] = $store['owner_id'];
                $storeOwnerIdMap[$store['owner_id']] = $store['store_id'];
            }
            $pixelUrls = $this->getPixelUrls($ownerIds);
            foreach ($pixelUrls as $pixel) {
                $storeId = $storeOwnerIdMap[$pixel['owner_id']];
                $this->updateStorePixelUrl($storeId, $pixel['url']);
            }
            return $pixelUrls;
        }
    }

    public function run()
    {
        try {
            $this->pollPixelUrls();
        } catch (Exception $e) {
            // don't crash the cron
        }
    }
}
