<?php

class VantageAnalytics_Analytics_Helper_Log extends Mage_Core_Helper_Abstract
{
    const LOGFILE = 'vantage-analytics.log';
    const MARKER = '[VantageAnalytics] ';

    protected function format($message, $findCaller=false)
    {
        if ($findCaller) {
            $callerInfo = debug_backtrace();
            $callerLine = $callerInfo[1]['line'];
            $callerFile = $callerInfo[1]['file'];
            return self::MARKER . " ({$callerFile}:{$callerLine}) {$message}";
        }
        return self::MARKER . " {$message}";
    }

    public function logInfo($msg)
    {
        Mage::log($this->format($msg), Zend_Log::INFO, self::LOGFILE);
    }

    public function logWarn($msg)
    {
        Mage::log($this->format($msg), Zend_Log::WARN, self::LOGFILE);
    }

    public function logError($msg)
    {
        $formattedMsg = $this->format($msg, true);
        $this->notifyVantage($formattedMsg);
        Mage::log($formattedMsg, Zend_Log::ERR, self::LOGFILE, true);
    }

    public function logException($e)
    {
        $msg = $this->format("\n" . $e->__toString(), true);
        $this->notifyVantage($msg);
        Mage::log($msg, Zend_Log::ERR, self::LOGFILE, true);
        Mage::logException($e);  // These always make it to a file named exception.log
    }

    protected function notifyVantage($message)
    {
        try {
            $username = Mage::helper("analytics/account")->username();

            $url = Mage::helper("analytics/account")->notifyVantageUrl();
            $channel = curl_init($url);
            curl_setopt($channel, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($channel, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($channel, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($channel, CURLOPT_CONNECTTIMEOUT_MS, 3200);
            curl_setopt($channel, CURLOPT_TIMEOUT_MS, 6200);

            $body = json_encode(array('username' => $username, 'message' => $message));
            curl_setopt($channel, CURLOPT_POSTFIELDS, $body);

            $headers = array(
                    'Content-type: application/json',
                    'Content-length: ' . strlen($body)
                );

            curl_setopt($channel, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($channel);
        } catch (Exception $e) {
            // Don't raise exceptions from an exception handler. 
        }
    }

}
