<?php

abstract class VantageAnalytics_Analytics_Model_Export_Base
{
    const PAGE_SIZE = 500;

    abstract protected function createCollection($website);

    public function __construct($pageSize = null, $api=null, $transformer='')
    {
        $this->transformer = $transformer;
        $this->pageSize = !is_null($pageSize) ? $pageSize : self::PAGE_SIZE;
        $this->api = (!is_null($api) ? $api :
            new VantageAnalytics_Analytics_Model_Api_RequestQueue());
    }

    protected function makeTransformer($entity)
    {
        return Mage::getModel("analytics/Transformer_{$this->transformer}", $entity);
    }

    protected function enqueue($data)
    {
        $this->api->enqueue('create', $data);
    }

    protected function exportEntity($entity)
    {
        $transformer = $this->makeTransformer($entity);
        $data = $transformer->toVantage();
        $this->enqueue($data);
    }

    protected function exportWebsite($website)
    {
        $collection = $this->createCollection($website);
        $totalPages = $collection->getLastPageNumber();
        $currentPage = 1;
        while ($currentPage <= $totalPages) {
            foreach ($collection as $entity) {
                $this->exportEntity($entity);
            }
            $currentPage++;
            $collection->clear();
        }
    }

    public function run()
    {
        $websites = Mage::app()->getWebsites();
        foreach ($websites as $website) {
           $this->exportWebsite($website);
        }
    }
}
