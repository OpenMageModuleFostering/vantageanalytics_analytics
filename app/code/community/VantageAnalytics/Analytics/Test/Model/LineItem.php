<?php

class VantageAnalytics_Analytics_Test_Model_LineItem extends VantageAnalytics_Analytics_Test_Model_Base
{
    public function setUp()
    {
        $tracking = $this->getMockBuilder('VantageAnalytics_Analytics_Model_Helper_Tracking')
            ->disableOriginalConstructor()->setMethods(array('setInitialCookie'))->getMock();
        $tracking->expects($this->any())->method('setInitialCookie');
        $this->replaceByMock('helper', 'analytics/tracking', $tracking);
    }

    /**
     * @test
     * @loadFixture
     * @doNotIndexAll
     */
    public function simpleOrder()
    {
        $order = Mage::getModel('sales/order')->load(1);
        $items = $order->getAllItems();
        $store = Mage::app()->getStore();

        $transformer =
            VantageAnalytics_Analytics_Model_Transformer_SalesOrderLineItem::factory($items[0], $store);

        $this->assertEquals("book", $transformer->sku());
        $this->assertEquals("1", $transformer->externalParentIdentifier());
        $this->assertEquals("13.00", $transformer->price());
        $this->assertEquals("lineItem", $transformer->entityType());
    }
}
