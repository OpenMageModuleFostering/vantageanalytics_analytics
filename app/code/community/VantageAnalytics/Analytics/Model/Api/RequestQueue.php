<?php

class VantageAnalytics_Analytics_Model_Api_RequestQueue
{
    public function enqueue($method, $resource)
    {
        $queue = Mage::helper('analytics/queue');
        $queue->enqueue(array(
            'class' => 'Api_Request',
            'method' => 'send',
            'args' => array($method, $resource)
        ));
    }
}
