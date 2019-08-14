<?php
class VantageAnalytics_Analytics_Model_Observer_SalesQuote extends VantageAnalytics_Analytics_Model_Observer_Base
{
    public function __construct()
    {
        parent::__construct('SalesQuote');
    }

    protected function getEntity($event)
    {
        return $event->getQuote();
    }

    protected function collectData($entity)
    {
        if(!Mage::helper('analytics/account')->isVerified()){
            return array();
        }
        $data = parent::collectData($entity);
        if (!array_key_exists('total_quantity', $data) || $data['total_quantity'] == 0) {
            return array();
        }
        if (!$this->isAdmin()) { // Get cookies from real shoppers, not site admins
            $tracking = Mage::helper('analytics/tracking')->getTrackingFromCookie();
            if ($tracking) {
                $data = array_merge($data, $tracking);
            }
        }
        return $data;
    }

    public function salesQuoteSaveAfter($observer)
    {
        $this->performSave($observer);
    }
}
