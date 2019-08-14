<?php

abstract class VantageAnalytics_Analytics_Model_Transformer_BaseSalesItem extends VantageAnalytics_Analytics_Model_Transformer_Base
{
    public abstract function externalParentIdentifier();
    public abstract function quantity();
    public abstract function price();

    public function externalIdentifier()
    {
        return $this->entity->getItemId();
    }

    public function storeIds()
    {
        return array($this->entity->getStore()->getWebsiteId());
    }

    public function externalProductId()
    {
        return $this->entity->getProductId();
    }

    protected function _magentoProduct()
    {
        return $this->entity->getProduct();
    }

    public function productName()
    {
        return $this->_magentoProduct()->getName();
    }

    public function sku()
    {
        return $this->_magentoProduct()->getSku();
    }

    public function entityType()
    {
        return "lineItem";
    }

    public function productOptions()
    {
        return VantageAnalytics_Analytics_Model_ProductOptions::factory($this->entity)
            ->options();
    }

    public function toVantage()
    {
        $lineData = array(
            "external_identifier"       => $this->externalIdentifier(),
            "store_ids"                 => $this->storeIds(),
            "source_created_at"         => $this->sourceCreatedAt(),
            "source_updated_at"         => $this->sourceUpdatedAt(),
            "external_product_id"       => $this->externalProductId(),
            "product_name"              => $this->productName(),
            "sku"                       => $this->sku(),
            "price"                     => $this->price(),
            "quantity"                  => $this->quantity(),
            "product_options"           => $this->productOptions()
        );

        return $lineData;
    }
}
