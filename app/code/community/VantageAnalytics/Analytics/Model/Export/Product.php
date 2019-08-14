<?php

class VantageAnalytics_Analytics_Model_Export_Product extends VantageAnalytics_Analytics_Model_Export_Base
{
    public function __construct($pageSize=null, $api=null)
    {
        parent::__construct($pageSize, $api, 'Product');
    }

    protected function createCollection($website)
    {
        return Mage::getModel('catalog/product')->getCollection()
            ->addWebsiteFilter($website->getId())
            ->addAttributeToSelect('*');
    }
}

