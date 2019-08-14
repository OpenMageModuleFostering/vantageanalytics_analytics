<?php

class VantageAnalytics_Analytics_Model_Api_Inline
{
    public function __construct()
    {
        $this->queue = array();
        $this->metaData = array();
    }

    public function enqueue($entityMethod, $entity, $isExport=false)
    {
        if ($entityMethod == 'progress') {
            $this->metaData = $entity;
        } else {
            $this->queue[] = $entity;
        }
    }

    public function isEmpty()
    {
        return true;
    }

    public function processQueue()
    {
        return; // no-op
    }
}
