<?php

class VantageAnalytics_Analytics_Model_Export_Customer extends VantageAnalytics_Analytics_Model_Export_Base
{
    public function __construct($pageSize=null, $api=null)
    {
        parent::__construct($pageSize, $api, 'Customer');
    }

    protected function createCollection($website)
    {
        return Mage::getModel('customer/customer')->getCollection()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('website_id', $website->getId())
            ->setPageSize(self::PAGE_SIZE);
    }
}
