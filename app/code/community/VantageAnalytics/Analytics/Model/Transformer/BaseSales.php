<?php

abstract class VantageAnalytics_Analytics_Model_Transformer_BaseSales extends VantageAnalytics_Analytics_Model_Transformer_Base
{
    public function __construct($magentoSalesEntity, $magentoStore)
    {
        parent::__construct($magentoSalesEntity, $magentoStore);

        $this->billingAddress = VantageAnalytics_Analytics_Model_Transformer_Address::factory(
            $this->entity->getBillingAddress(), 'billing');

        $this->shippingAddress = VantageAnalytics_Analytics_Model_Transformer_Address::factory(
            $this->entity->getShippingAddress(), 'shipping');
    }

    protected abstract function orderStatus();
    protected abstract function paymentStatus();
    protected abstract function fulfillmentStatus();
    protected abstract function currencyCode();
    protected abstract function processingMethod();
    protected abstract function cardType();
    protected abstract function totalQuantity();
    protected abstract function refund();


    protected abstract function lineItems();
    protected abstract function buildLine($item);

    // lines returns line items for internal use
    protected  function _lines()
    {
        return $this->entity->getAllVisibleItems();
    }

    protected function storeIds()
    {
        return array($this->entity->getStore()->getWebsiteId());
    }

    protected function externalIdentifier()
    {
        return $this->entity->getEntityId();
    }

    protected function sourceName()
    {
        return 'web';
    }

    protected function customerExternalId()
    {
        $customerId = $this->entity->getCustomerId();
        return strlen($customerId) > 0 ? $customerId : null;
    }

    protected function customerFirstName()
    {
        return $this->entity->getCustomerFirstname();
    }

    protected function customerLastName()
    {
        return $this->entity->getCustomerLastname();
    }

    protected function customerCompany()
    {
        // Confirmed the customer doesn't have company
        // you can customize magento to make company
        // a required field, but it's not part of the
        // default install
        return $this->billingAddress->company();
    }

    protected function customerEmail()
    {
        return $this->entity->getCustomerEmail();
    }

    protected function landingSite()
    {
        // XXX to implement
    }

    protected function referringSite()
    {
        // XXX to implement
    }

    protected function subtotal()
    {
        return $this->entity->getSubtotal();
    }

    protected function tax()
    {
        return $this->entity->getTaxAmount();
    }

    protected function discount()
    {
        return $this->entity->getDiscountAmount();
    }

    protected function shippingCost()
    {
        return $this->entity->getShippingAmount();
    }

    protected function total()
    {
        return $this->entity->getGrandTotal();
    }

    protected function externalParentIdentifier()
    {
        return NULL;
    }

    public function toVantage()
    {
        $quoteData = array(
            "external_identifier" => $this->externalIdentifier(),
            "store_ids" => $this->storeIds(),
            "source_created_at" => $this->sourceCreatedAt(),
            "source_updated_at" => $this->sourceUpdatedAt(),
            "source_name" => $this->sourceName(),
            "customer_external_id" => $this->customerExternalId(),
            "customer_first_name" => $this->customerFirstName(),
            "customer_last_name" => $this->customerLastName(),
            "customer_company" => $this->customerCompany(),
            "customer_email" => $this->customerEmail(),
            "order_status" => $this->orderStatus(),
            "payment_status" => $this->paymentStatus(),
            "fulfillment_status" => $this->fulfillmentStatus(),
            "landing_site" => $this->landingSite(),
            "referring_site" => $this->referringSite(),
            "currency_code" => $this->currencyCode(),
            "processing_method" => $this->processingMethod(),
            "card_type" => $this->cardType(),
            "total_quantity" => $this->totalQuantity(),
            "subtotal" => $this->subtotal(),
            "tax" => $this->tax(),
            "discount" => $this->discount(),
            "shipping_cost" => $this->shippingCost(),
            "total" => $this->total(),
            "refund" => $this->refund(),
            "entity_type" => $this->entityType(),
            "external_parent_identifier" => $this->externalParentIdentifier(),
            "line_items" => $this->lineItems()
        );

        $shippingAddressData = $this->shippingAddress->toVantage();
        $billingAddressData = $this->billingAddress->toVantage();
        $data = array_merge($quoteData, $shippingAddressData, $billingAddressData);

        return $data;
    }
}
