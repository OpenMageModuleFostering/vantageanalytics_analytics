<?php
class VantageAnalytics_Analytics_Model_Observer_SalesOrder extends VantageAnalytics_Analytics_Model_Observer_Base
{
    public function __construct()
    {
        parent::__construct('SalesOrder');
    }

    protected function getEntity($event)
    {
        return $event->getOrder();
    }

    protected function collectData($entity)
    {
        $data = parent::collectData($entity);

        // Collect cookies from real shoppers and not site admins.
        if (!$this->isAdmin()) {
            $tracking = Mage::helper('analytics/tracking')->getTrackingFromCookie();
            if ($tracking) {
                $data = array_merge($data, $tracking);
            }
        }

        return $data;
    }

    public function salesOrderSaveAfter($observer)
    {
        $this->performSave($observer);
    }

    public function salesOrderPlaceAfter($observer)
    {
        $this->performSave($observer);
    }

    public function salesOrderDeleteAfter($observer)
    {
        $this->performDelete($observer);
    }
}
