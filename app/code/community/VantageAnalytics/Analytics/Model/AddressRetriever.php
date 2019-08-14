<?php

class VantageAnalytics_Analytics_Model_AddressRetriever
{
    public function __construct($customer)
    {
        $this->customer = $customer;
    }

    public static function factory($customer)
    {
        return new self($customer);
    }

    protected function addressId()
    {
        return  $this->customer->getDefaultBilling();
    }

    public function address()
    {
        return Mage::getModel('customer/address')->load($this->addressId());
    }

}