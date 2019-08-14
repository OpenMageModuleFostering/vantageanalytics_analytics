<?php

class VantageAnalytics_Analytics_Model_Api_RequestQueue
{
    public function enqueue($entityMethod, $entity, $isExport=false)
    {
        // Api_Request::export was added after Api_Request::send to avoid
        // potential serialization issues I have run into in the past when
        // changing method signatures on serialized objects (these objects
        // get serialized into the vantage_queue table).
        if ($isExport) {
            $method = 'export';
        } else {
            $method = 'send';
        }

        $queue = Mage::helper('analytics/queue');
        $queue->enqueue(array(
            'class' => 'Api_Request',
            'method' => $method,
            'args' => array($entityMethod, $entity)
        ));
    }
}
