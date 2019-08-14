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

    protected function findStoreByWebsiteId($websiteId)
    {
        foreach (Mage::app()->getWebsites() as $website) {
            if ($website->getId() == $websiteId) {
                return $website->getDefaultGroup()->getDefaultStore();
            }
        }
    }

    public function catalogProductSaveAfter($observer)
    {
        $product = $this->getEntity($observer->getEvent());
        $storeId = Mage::app()->getStore()->getId();

        foreach ($product->getWebsiteIds() as $websiteId) {
            $store = $this->findStoreByWebsiteId($websiteId);
            Mage::app()->setCurrentStore($store->getStoreId());
            $this->performSave($observer, $store);
        }

        Mage::app()->setCurrentStore($storeId);
    }

    public function catalogProductDeleteAfter($observer)
    {
        $product = $this->getEntity($observer->getEvent());
        $storeId = Mage::app()->getStore()->getId();

        foreach ($product->getWebsiteIds() as $websiteId) {
            $store = $this->findStoreByWebsiteId($websiteId);
            Mage::app()->setCurrentStore($store->getStoreId());
            $this->performDelete($observer, $store);
        }

        Mage::app()->setCurrentStore($storeId);
    }
}
