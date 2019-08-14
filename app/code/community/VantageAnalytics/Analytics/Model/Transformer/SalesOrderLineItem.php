<?php

class VantageAnalytics_Analytics_Model_Transformer_SalesOrderLineItem extends VantageAnalytics_Analytics_Model_Transformer_BaseSalesItem
{
    public static function factory($magentoSalesOrderLineItem, $magentoStore)
    {
        return new self($magentoSalesOrderLineItem, $magentoStore);
    }

    public function storeIds()
    {
        $storeId = $this->entity->getStoreId();
        return array(
            Mage::getModel('core/store')->load($storeId)->getWebsiteId()
        );
    }

    public function externalIdentifier()
    {
        return $this->entity->getItemId();
    }

    public function externalOrderIdentifier()
    {
        return $this->entity->getOrderId();
    }

    public function externalProductId()
    {
        return $this->entity->getProductId();
    }

    private function magentoProduct()
    {
        return $this->entity->getProduct();
    }

    public function productName()
    {
        return $this->magentoProduct()->getName();
    }

    public function sku()
    {
        return $this->magentoProduct()->getSku();
    }

    public function externalParentIdentifier()
    {
        return $this->entity->getOrderId();
    }

    public function quantity()
    {
        // Get total item quantity *ordered*
        return $this->entity->getQtyOrdered();
    }

    public function price()
    {
        // price here is the item price, w/o discounts applied.
        return $this->entity->getOriginalPrice();
    }

}
