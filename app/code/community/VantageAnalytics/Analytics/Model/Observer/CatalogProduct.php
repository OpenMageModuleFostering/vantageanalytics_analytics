<?php
class VantageAnalytics_Analytics_Model_Observer_CatalogProduct extends VantageAnalytics_Analytics_Model_Observer_Base
{
    public function __construct()
    {
        parent::__construct("Product");
    }

    protected function getEntity($event)
    {
        return $event->getProduct();
    }

    public function catalogProductSaveAfter($observer)
    {
        $this->performSave($observer);
    }

    public function catalogProductDeleteAfter($observer)
    {
        $this->performDelete($observer);
    }
}
