<?php

class VantageAnalytics_Analytics_Model_Cron
{
    public function __construct()
    {
        $this->lockfile = null;
    }

    protected function log($msg)
    {
        Mage::helper('analytics/log')->logInfo('Cron ' . $msg);
    }

    protected function jitter()
    {
        $seconds = rand(0, 5);
        $this->log("Sleeping for {$seconds} seconds");
        sleep($seconds);
    }

    protected function ensureLockDirectoryExists()
    {
        $dir = Mage::getBaseDir('var').DS.'locks';
        if (!file_exists($dir)) {
            mkdir($dir);
        }
    }

    protected function getCronLockfile()
    {
        $this->ensureLockDirectoryExists();

        if (is_null($this->lockfile)) {
            $this->log('lock file opened');
            $this->lockfile = fopen(Mage::getBaseDir('var').DS.'locks'.DS.'vantage_cron_lock', 'w');
        }
        return $this->lockfile;
    }

    public function acquireCronLock()
    {
        $this->log('attempting to acquire the lock');
        $file = $this->getCronLockfile();
        return flock($file, LOCK_EX | LOCK_NB);
    }

    public function releaseCronLock()
    {
        // Not strictly necessary because the process will close the handle on exit anyway
        $this->log('lock file closed');
        fclose($this->lockfile);
    }

    public function runHistoricalExport()
    {
        $export = Mage::getModel('analytics/Export_Runner');
        $export->run();
    }

    public function pollPixelUrls()
    {
        $pixel = Mage::getModel('analytics/Pixel');
        $pixel->run();
    }

    protected function accountIsVerified()
    {
        return Mage::helper('analytics/account')->isVerified();
    }

    protected function cronIsEnabled()
    {
        return Mage::helper('analytics/account')->isCronEnabled();
    }

    public function run()
    {
        if (!$this->cronIsEnabled()) {
            return;
        }

        if (!$this->accountIsVerified()) {
            $this->log('account verification required or failed. Not running.');
            return;
        }

        set_time_limit(0);
        proc_nice(19); // lower priority - try not to hog CPU

        $this->jitter();

        if (!$this->acquireCronLock()) {
            $this->log('lock not acquired - cron is already running');
            return;
        }

        $this->pollPixelUrls();

        $this->runHistoricalExport();

        $this->log('processing the queue');
        $queue = Mage::helper('analytics/queue');
        $queue->processQueue();

        $this->log('the queue is empty');
        $this->releaseCronLock();
    }
}
