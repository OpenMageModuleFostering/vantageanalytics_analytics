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
        Mage::log($this->format($msg, true), Zend_Log::ERR, self::LOGFILE, true);
    }

    public function logException($e)
    {
        Mage::log($this->format("\n" . $e->__toString(), true), Zend_Log::ERR, self::LOGFILE, true);
        Mage::logException($e);  // These always make it to a file named exception.log
    }
}
