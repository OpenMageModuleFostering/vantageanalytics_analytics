<?php

class VantageAnalytics_Analytics_Model_Export_Order extends VantageAnalytics_Analytics_Model_Export_Base
{
    public function __construct($pageSize=null, $api=null)
    {
        parent::__construct($pageSize, $api, 'SalesOrder');
    }

    protected function createCollection($website)
    {
        $filter = array();
        foreach ($website->getStoreIds() as $storeId) {
            $filter[] = array('eq', $storeId);
        }
        return Mage::getModel('sales/order')->getCollection()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('store_id', $filter)
            ->setPageSize($this->pageSize);
    }
}
