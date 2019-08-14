<?php

class VantageAnalytics_Analytics_Model_Export_Customer extends VantageAnalytics_Analytics_Model_Export_Base
{
    public function __construct($pageSize=null, $api=null)
    {
        parent::__construct($pageSize, $api, 'Customer');
    }

    protected function createCollection($website, $pageNumber)
    {
        $collection = Mage::getModel('customer/customer')->getCollection()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('website_id', $website->getId())
            ->setPageSize($this->pageSize);
        if (!is_null($pageNumber)) {
            $collection->setPage($pageNumber, $this->pageSize);
        }
        return $collection;
    }
}
