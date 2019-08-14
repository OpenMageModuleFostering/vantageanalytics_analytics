<?php

class VantageAnalytics_Analytics_Model_Export_Store extends VantageAnalytics_Analytics_Model_Export_Base
{
    public function __construct($pageSize=null, $api=null)
    {
        parent::__construct($pageSize, $api, 'Store');
    }

    protected function createCollection($website)
    {
        // stub out abstract function from base class
    }

    public function exportWebsite($website)
    {
        $this->exportEntity($website);
    }
}

