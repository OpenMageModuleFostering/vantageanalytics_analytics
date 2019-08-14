<?php

class VantageAnalytics_Analytics_Model_Transformer_SalesQuoteLineItem extends VantageAnalytics_Analytics_Model_Transformer_BaseSalesItem
{
    public static function factory($magentoSalesQuoteLineItem)
    {
        return new self($magentoSalesQuoteLineItem);
    }

    public function externalParentIdentifier()
    {
        return $this->entity->getQuoteId();
    }

    public function quantity()
    {
        // Get total item quantity (include parent item relation)
        // I have very little idea of what this means (now)
        return $this->entity->getTotalQty();
    }

    public function price()
    {
        // price here is the item price, w/o discounts applied.
        return $this->_magentoProduct()->getPrice();
    }
}
