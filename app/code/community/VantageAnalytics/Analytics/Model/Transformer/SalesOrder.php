<?php

class VantageAnalytics_Analytics_Model_Transformer_SalesOrder extends VantageAnalytics_Analytics_Model_Transformer_BaseSales
{
    public static function factory($magentoSalesEntity, $magentoStore)
    {
        return new self($magentoSalesEntity, $magentoStore);
    }

    public function __construct($magentoSalesEntity, $magentoStore)
    {
        parent::__construct($magentoSalesEntity, $magentoStore);

        $this->statuses = VantageAnalytics_Analytics_Helper_Statuses::factory($magentoSalesEntity);
    }

    public function entityType()
    {
        return "order";
    }

    protected function orderStatus()
    {
        return $this->statuses->orderStatus();
    }

    protected function paymentStatus()
    {
        return $this->statuses->paymentStatus();
    }

    protected function fulfillmentStatus()
    {
        return $this->statuses->fulfillmentStatus();
    }

    protected function landingSite()
    {
        // XXX - this requires that we sniff out tracking cookies
    }

    protected function referringSite()
    {
        // XXX - this requires that we sniff out tracking cookies
    }

    protected function currencyCode()
    {
        return $this->entity->getOrderCurrency()->getCode();
    }

    protected function processingMethod()
    {
        $payment = $this->entity->getPayment();
        if ($payment)
        {
            return $payment->getCode();
        }
    }

    protected function cardType()
    {
        $payment = $this->entity->getPayment();

        return $payment ? $payment->getCcTypeName() : NULL;
    }

    protected function refund()
    {
        return $this->entity->getData('total_refunded');
    }

    public function externalParentIdentifier()
    {
        return $this->entity->getQuoteId();
    }

    protected function totalQuantity()
    {
        // Total item is only avaialable on order
        $total = 0;

        foreach ($this->_lines() as $item) {
            $total += $item->getQtyOrdered();
        }

        return $total;
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
        return VantageAnalytics_Analytics_Model_Transformer_SalesOrderLineItem::factory($item, $this->magentoStore)
            ->toVantage();
    }
}
