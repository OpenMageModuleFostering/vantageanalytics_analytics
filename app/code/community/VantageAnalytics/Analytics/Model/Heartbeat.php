<?php

class VantageAnalytics_Analytics_Model_Heartbeat
{
    public function __construct()
    {
        $this->api = new VantageAnalytics_Analytics_Model_Api_Request();
    }

    public function send()
    {
        $data = VantageAnalytics_Analytics_Model_Debug::factory()->toVantage();

        $this->api->send('create', $data);
    }
}
