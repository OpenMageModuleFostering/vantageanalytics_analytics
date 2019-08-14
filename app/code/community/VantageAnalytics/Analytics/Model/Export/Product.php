<?php

class VantageAnalytics_Analytics_Model_Export_Product extends VantageAnalytics_Analytics_Model_Export_Base
{
    public function __construct($pageSize=null, $api=null)
    {
        parent::__construct($pageSize, $api, 'Product');
    }

    protected function createCollection($website, $pageNumber)
    {
        $storeId = $website->getDefaultGroup()->getDefaultStoreId();
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addWebsiteFilter($website->getId())
            ->setStoreId($storeId)
            ->addAttributeToSelect('*')
            ->addUrlRewrite()
            ->setPageSize($this->pageSize);
        if (!is_null($pageNumber)) {
            $collection->setPage($pageNumber, $this->pageSize);
        }
        return $collection;
    }
}

