<?php

class VantageAnalytics_Analytics_Model_Api_Webhook
{
    public function __construct($resource, $method)
    {
        $this->resource = $resource;
        $this->method = $method;
    }

    public static function factory($resource, $method)
    {
        return new self($resource, $method);
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
                "type" => $this->type()
            ),
        );
    }

    public function body()
    {
        return array("body" => $this->resource);
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
        return array_key_exists('entity_type', $this->resource) ? $this->resource['entity_type'] : "object";
    }
}
