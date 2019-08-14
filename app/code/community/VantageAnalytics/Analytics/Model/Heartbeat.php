<?php

class VantageAnalytics_Analytics_Model_Heartbeat
{
    public function __construct()
    {
        $this->api = new VantageAnalytics_Analytics_Model_Api_Request();
    }

    public function send()
    {
        if (!Mage::helper('analytics/account')->isCronEnabled()) {
            return;
        }

        if (Mage::helper('analytics/account')->isVerified()) {
            $data = VantageAnalytics_Analytics_Model_Debug::factory()->toVantage();

            $this->api->send('create', $data);
        }
    }
}
