<?php

class VantageAnalytics_Analytics_Model_Export_Order extends VantageAnalytics_Analytics_Model_Export_Base
{
    public function __construct($pageSize=null, $api=null)
    {
        parent::__construct($pageSize, $api, 'SalesOrder');
    }

    protected function createCollection($website, $pageNumber)
    {
        $filter = array();
        foreach ($website->getStoreIds() as $storeId) {
            $filter[] = array('eq', $storeId);
        }
        if (count($filter) <= 0) {
            return null;
        }

        $orderCollection = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('store_id', $filter)
            ->setPageSize($this->pageSize);

        if (!is_null($pageNumber)) {
            $orderCollection->setPage($pageNumber, $this->pageSize);
            $orderIds = $orderCollection->getAllIds($this->pageSize, $pageNumber * $this->pageSize);
            if (count($orderIds) > 0) {

                $lineItemCollection = Mage::getResourceModel('sales/order_item_collection')
                    ->setOrderFilter($orderIds);

                $productIds = array();
                foreach ($lineItemCollection as $lineItem) {
                    if (!$lineItem->isDeleted() && !$lineItem->getParentItemId()) {
                        $order = $orderCollection->getItemById($lineItem->getOrderId());
                        if ($order) {
                            $lineItem->setOrder($order);
                        }
                        $productIds[] = $lineItem->getProductId();
                    }
                }

                $productCollection = Mage::getModel('catalog/product')->getCollection()
                    ->addIdFilter($productIds);

                foreach ($lineItemCollection as $lineItem) {
                    $product = $productCollection->getItemById($lineItem->getProductId());
                    if ($product) {
                        $lineItem->setProduct($product);
                        $lineItem->setName($product->getName());
                        $lineItem->setPrice($product->getPrice());
                    }
                }
            }
        }

        return $orderCollection;
    }
}
