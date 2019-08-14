<?php

class VantageAnalytics_Analytics_Test_Model_Customer extends VantageAnalytics_Analytics_Test_Model_Base
{
    /**
     * @test
     * @loadFixture
     * @doNotIndexAll
     */
    public function simpleCustomer()
    {
        $customer = Mage::getModel('customer/customer')->load(1);
        $store = Mage::app()->getStore();

        $transformer =
            VantageAnalytics_Analytics_Model_Transformer_Customer::factory($customer, $store);

        $this->assertEquals("example@example.com", $transformer->email());
        $this->assertEquals("john", $transformer->firstName());
        $this->assertEquals("smith", $transformer->lastName());
        $this->assertEquals('2012-08-17T18:00:41+00:00', $transformer->sourceCreatedAt());
    }

    /**
     * @test
     * @loadFixture simpleCustomer
     * @doNotIndexAll
     */
    public function vantageCustomerTest()
    {
        $customer = Mage::getModel('customer/customer')->load(1);
        $store = Mage::app()->getStore();

        $transformer =
            VantageAnalytics_Analytics_Model_Transformer_Customer::factory($customer, $store);

        $data = $transformer->toVantage();

        $this->assertEquals(NULL, $data['external_address_id']);
        $this->assertEquals('customer', $data['entity_type']);
    }
}
