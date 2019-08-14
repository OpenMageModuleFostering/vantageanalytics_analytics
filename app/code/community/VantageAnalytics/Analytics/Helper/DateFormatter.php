<?php
class VantageAnalytics_Analytics_Helper_DateFormatter
{
    public static function factory($dateString)
    {
        return new self($dateString);
    }

    public function __construct($dateString)
    {
        $this->dateString = $dateString;
    }

    public function defaultTimezone()
    {
        return new DateTimeZone("UTC");
    }

    public function dateFormat()
    {
        return "c";
    }

    public function toDate()
    {
        $datetime = new DateTime($this->dateString);

        return $datetime->format($this->dateFormat());
    }

    public function toUtcDate()
    {
        $datetime = new DateTime($this->dateString);

        $datetime->setTimeZone($this->defaultTimezone());

        return $datetime->format($this->dateFormat());
    }

}
