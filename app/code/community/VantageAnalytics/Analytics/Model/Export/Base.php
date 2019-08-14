<?php

abstract class VantageAnalytics_Analytics_Model_Export_Base
{
    const PAGE_SIZE = 500;

    abstract protected function createCollection($website, $pageNumber);

    public function __construct($pageSize = null, $api=null, $transformer='')
    {
        $this->transformer = $transformer;
        $this->pageSize = !is_null($pageSize) ? $pageSize : self::PAGE_SIZE;
        $this->api = (!is_null($api) ? $api :
            new VantageAnalytics_Analytics_Model_Api_RequestQueue());
    }

    protected function makeTransformer($entity, $store)
    {
        $transformClass = "VantageAnalytics_Analytics_Model_Transformer_" . $this->transformer;
        return new $transformClass($entity, $store);
    }

    protected function enqueue($data)
    {
        $this->api->enqueue('create', $data, true);
    }

    protected function getEntityName()
    {
        if ($this->transformer == 'SalesOrder') {
            return 'Order';
        } else {
            return $this->transformer;
        }
    }

    public function exportEntity($entity, $store)
    {
        $transformer = $this->makeTransformer($entity, $store);
        $data = $transformer->toVantage();
        $this->enqueue($data);
    }

    protected function _exportMetaData($websiteId, $entity, $currentPage, $totalPages, $pageSize, $totalItems)
    {
        $this->api->enqueue(
            'progress',
            array(
                'store_ids' => array($websiteId),
                'entity_type' => $entity,
                'current_page' => $currentPage,
                'total_pages' => $totalPages,
                'page_size' => $pageSize,
                'total_items' => $totalItems
            ),
            true
        );
    }

    public function exportMetaData($websiteId)
    {
        $websites = Mage::app()->getWebsites();
        foreach ($websites as $website) {
            if ($websiteId == $website->getWebsiteId()) {
                $store = $website->getDefaultGroup()->getDefaultStore();
                Mage::app()->setCurrentStore($store->getStoreId());

                $collection = $this->createCollection($website, 1);
                $totalPages = $collection->getLastPageNumber();
                $totalItems = $collection->getSize();
                $entityName = $this->getEntityName();

                $this->_exportMetaData(
                    $website->getWebsiteId(),
                    strtolower($entityName),
                    $currentPage,
                    $totalPages,
                    $this->pageSize,
                    $totalItems
                );
            }
        }
    }

    public function exportPage($websiteId, $startPage, $endPage)
    {
        $websites = Mage::app()->getWebsites();
        foreach ($websites as $website) {
            if ($websiteId == $website->getWebsiteId()) {
                $store = $website->getDefaultGroup()->getDefaultStore();
                Mage::app()->setCurrentStore($store->getStoreId());

                $currentPage = $startPage;
                while ($currentPage < $endPage) {
                    $collection = $this->createCollection($website, $currentPage);
                    $totalPages = $collection->getLastPageNumber();
                    $totalItems = $collection->getSize();
                    $entityName = $this->getEntityName();

                    $this->_exportMetaData(
                        $website->getWebsiteId(),
                        strtolower($entityName),
                        $currentPage,
                        $totalPages,
                        $this->pageSize,
                        $totalItems
                    );

                    foreach ($collection as $entity) {
                        $this->exportEntity($entity, $store);
                        try {
                            $entity->clearInstance();
                        } catch (Exception $e) {
                            Mage::helper('analytics/log')->logException($e);
                        }
                    }

                    Mage::helper('analytics/log')->logInfo("Completed page ${currentPage}");

                    $this->processQueue();

                    $currentPage++;

                    $collection->clear();
                }
            }
        }
    }

    protected function processQueue()
    {
        $this->api->processQueue();
    }
}
