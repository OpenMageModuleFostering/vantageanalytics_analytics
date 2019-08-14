<?php

class VantageAnalytics_Analytics_Model_Api_Signature
{
    const HEADER = 'X-Vantage-Hmac-SHA256';

    public function __construct($message, $secret)
    {
        $this->message = $message;
        $this->secret = $secret;
    }

    public function sign()
    {
       return hash_hmac('sha256', $this->message, $this->secret);
    }

    public function signatureHeader()
    {
        $header = self::HEADER;
        $signature = $this->sign();
        return "{$header}: {$signature}";
    }
}

