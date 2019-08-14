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

        $this->notifyExportStart();
        $this->setExportDone(); // Hope for the best

        $entities = array('Store', 'Customer', 'Product', 'Order');
        Mage::helper('analytics/log')->logInfo("Start exporting all entities");
        foreach ($entities as $entity) {
            try {
                Mage::helper('analytics/log')->logInfo("Exporting ". $entity);
                $exporter = Mage::getModel('analytics/Export_' . $entity);
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

    public function setExportDone($done=1)
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
