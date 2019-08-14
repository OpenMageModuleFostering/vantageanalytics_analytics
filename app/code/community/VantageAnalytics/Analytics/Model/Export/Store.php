<?php

class VantageAnalytics_Analytics_Model_Export_Store extends VantageAnalytics_Analytics_Model_Export_Base
{
    public function __construct($pageSize=null, $api=null)
    {
        parent::__construct($pageSize, $api, 'Store');
    }

    protected function createCollection($website, $pageNumber)
    {
        // stub out abstract function from base class
    }

    public function exportWebsite($website)
    {
        $store = $website->getDefaultGroup()->getDefaultStore();
        Mage::app()->setCurrentStore($store->getStoreId());
        $this->exportEntity($website, $store);
    }
}

