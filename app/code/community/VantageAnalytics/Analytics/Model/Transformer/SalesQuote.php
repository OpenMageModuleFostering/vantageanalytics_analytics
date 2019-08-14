<?php

class VantageAnalytics_Analytics_Model_Transformer_SalesQuote extends VantageAnalytics_Analytics_Model_Transformer_BaseSales
{
    public static function factory($magentoSalesEntity, $magentoStore)
    {
        return new self($magentoSalesEntity, $magentoStore);
    }

    public function entityType()
    {
        return "quote";
    }

    private function _store()
    {
        $store = $this->entity->getStore();
        return VantageAnalytics_Analytics_Model_Transformer_Store::factory($store->getWebsite());
    }

    public function orderStatus()
    {
        return "open";
    }

    public function paymentStatus()
    {
        return "unpaid";
    }

    public function fulfillmentStatus()
    {
        return "unfulfilled";
    }

    public function processingMethod()
    {
        return NULL;
    }

    public function cardType()
    {
        return NULL;
    }

    public function refund()
    {
        return 0.0000;
    }

    // Currency logic
    // global - currency which is set for default in backend
    // base - currency which is set for current website. all attributes that
    // have 'base_' prefix saved in this currency
    // store - all the time it was currency of website and all attributes
    // quote/order - currency which was selected by customer or configured by
    // admin for current store. currency in which customer sees
    // price thought all checkout.
    public function currencyCode()
    {
        return $this->_store()->currencyCode();
    }

    public function totalQuantity()
    {
        return $this->entity->getItemsQty();
    }

    protected function lineItems()
    {
        $lines = array();

        foreach ($this->_lines() as $item) {
            $lines[] = $this->buildLine($item);
        }

        return $lines;
    }

    protected function buildLine($item)
    {
        return VantageAnalytics_Analytics_Model_Transformer_SalesQuoteLineItem::factory($item)
            ->toVantage();
    }
}
