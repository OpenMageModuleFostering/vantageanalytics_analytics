<?php

class VantageAnalytics_Analytics_Model_Api_Webhook
{
    public function __construct($entity, $method, $isExport=false)
    {
        $this->entity = $entity;
        $this->method = $method;
        $this->isExport = $isExport;
    }

    public static function factory($entity, $method, $isExport=false)
    {
        return new self($entity, $method, $isExport);
    }

    public function getPostData()
    {
        return array_merge($this->preamble(), $this->body());
    }

    public function preamble()
    {
        return array(
            "webhook" => array(
                "created" => $this->createdAt(),
                "type" => $this->type(),
                "isExport" => $this->isExport
            ),
        );
    }

    public function body()
    {
        return array("body" => $this->entity);
    }

    private function type()
    {
        return $this->_name() . "." . $this->method;
    }

    private function createdAt()
    {
        $date = gmdate('Y-m-d H:i:s');

        return VantageAnalytics_Analytics_Helper_DateFormatter::factory($date)->toDate();
    }

    private function _name()
    {
        return array_key_exists('entity_type', $this->entity) ? $this->entity['entity_type'] : "object";
    }
}
