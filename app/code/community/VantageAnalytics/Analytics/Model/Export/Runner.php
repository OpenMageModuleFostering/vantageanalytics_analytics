<?php

class VantageAnalytics_Analytics_Model_Export_Runner
{
    protected function enqueue($method, $entity)
    {
        $api = new VantageAnalytics_Analytics_Model_Api_RequestQueue();
        $api->enqueue($method, $entity, true);
    }

    public function run()
    {
        if ($this->isExportDone()) {
            return;
        }

        if (!gc_enabled()) {
            Mage::helper('analytics/log')->logError("GC was not enabled. I am enabling it manually.");
            gc_enable();
        }

        $this->notifyExportStart();
        $this->setExportDone(1);

        $entities = array('Store', 'Order', 'Customer', 'Product');
        Mage::helper('analytics/log')->logInfo("Start exporting all entities");
        foreach ($entities as $entity) {
            try {
                Mage::helper('analytics/log')->logInfo("Exporting ". $entity);
                $exportClass = "VantageAnalytics_Analytics_Model_Export_" . $entity;
                $exporter = new $exportClass;
                $exporter->run();
            } catch (Exception $e) {
                Mage::helper('analytics/log')->logError("Failed to export ". $entity);
                Mage::helper('analytics/log')->logException($e);
            }
        }

        $this->notifyExportComplete();
        Mage::helper('analytics/log')->logInfo("Done exporting all entities");
    }

    public function isExportDone()
    {
        return Mage::helper('analytics/account')->isExportDone();
    }

    public function setExportDone($done)
    {
        return Mage::helper('analytics/account')->setExportDone($done);
    }

    protected function websiteIds()
    {
        $websites = Mage::app()->getWebsites();
        $websiteIds = array();
        foreach ($websites as $website) {
            $websiteIds[] = $website->getId();
        }
        return $websiteIds;
    }

    public function notifyExportStart()
    {
        foreach ($this->websiteIds() as $websiteId) {
            $exportStarting = array('entity_type' => 'export', 'store_ids' => array($websiteId));
            $this->enqueue('start', $exportStarting);
        }
    }

    public function notifyExportComplete()
    {
        foreach ($this->websiteIds() as $websiteId) {
            $exportComplete = array('entity_type' => 'export', 'store_ids' => array($websiteId));
            $this->enqueue('complete', $exportComplete);
        }
    }
}
