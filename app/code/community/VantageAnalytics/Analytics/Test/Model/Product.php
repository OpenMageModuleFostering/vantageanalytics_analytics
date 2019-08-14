<?php

class VantageAnalytics_Analytics_Test_Model_Product extends VantageAnalytics_Analytics_Test_Model_Base
{
    /**
     * @test
     * @loadFixture
     * @doNotIndexAll
     */
    public function simpleProduct()
    {
        $price = 70.50;
        $product = Mage::getModel('catalog/product')->load(1);
        $product->setPrice($price);
        $store = Mage::app()->getStore();

        $transformer =
            VantageAnalytics_Analytics_Model_Transformer_Product::factory($product, $store);

        $this->assertEquals("book", $transformer->sku());
        $this->assertEquals('2012-08-17T18:00:41+00:00', $transformer->sourceCreatedAt());
        $this->assertEquals($price, $transformer->price());
    }

    /**
     * @test
     * @loadFixture simpleProduct
     * @doNotIndexAll
     */
    public function toVantageTest()
    {
        $product = Mage::getModel('catalog/product')->load(1);
        $store = Mage::app()->getStore();

        $transformer =
            VantageAnalytics_Analytics_Model_Transformer_Product::factory($product, $store);

        $data = $transformer->toVantage();
        $this->assertEquals('product', $data['entity_type']);
    }

    /**
     * @test
     * @loadFixture
     * @doNotIndexAll
     */
    public function parentProduct()
    {
        $product = Mage::getModel('catalog/product')->load(410955);
        $store = Mage::app()->getStore();

        $parentId = VantageAnalytics_Analytics_Model_ParentProduct::factory($product, $store)->id();

        $this->assertEquals(336835, $parentId);
    }

}
