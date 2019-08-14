<?php

class VantageAnalytics_Analytics_Test_Model_Order extends VantageAnalytics_Analytics_Test_Model_Base
{
    protected function getTransformedOrder($state=null, $orderid=1)
    {
        $order = Mage::getModel('sales/order')->load(1);
        if ($state) {
            $order->setState($state);
        }
        $store = Mage::app()->getStore(1);
        $transform = VantageAnalytics_Analytics_Model_Transformer_SalesOrder::factory($order, $store);
        return $transform->toVantage();
    }

    /**
     * @test
     * @loadFixture
     * @dataProvider dataProvider
     * @doNotIndexAll
     */
    public function orderStatus($state, $status)
    {
        $dict = $this->getTransformedOrder($state);

        $this->assertEquals($status, $dict['order_status']);
    }

    /**
     * @test
     * @loadFixture
     * @doNotIndexAll
     */
    public function orderStatusComplete()
    {
        $dict = $this->getTransformedOrder();
        $this->assertEquals('closed', $dict['order_status']);
    }

    /**
     * @test
     * @loadFixture
     * @doNotIndexAll
     */
    public function orderStatusCanceled()
    {
        $dict = $this->getTransformedOrder();
        $this->assertEquals('canceled', $dict['order_status']);
    }

    /**
     * @test
     * @loadFixture
     * @doNotIndexAll
     */
    public function paymentStatusUnpaid()
    {
        $dict = $this->getTransformedOrder();
        $this->assertEquals('unpaid', $dict['payment_status']);
    }

    /**
     * @test
     * @loadFixture orderStatus
     * @doNotIndexAll
     */
    public function entityType()
    {
        $dict = $this->getTransformedOrder();
        $this->assertEquals("order" , $dict['entity_type']);
    }

}
