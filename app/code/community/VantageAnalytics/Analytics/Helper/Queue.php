<?php

class VantageAnalytics_Analytics_Helper_Queue extends Mage_Core_Helper_Abstract
{
    protected $queueName = "vantage";
    protected $registry = array();
    protected $maxBatchSize = 50;

    protected function getQueue()
    {
        if (!isset($this->registry[$this->queueName]))
        {
            $config  = Mage::getConfig()->getResourceConnectionConfig("default_setup");
            $queueOptions = array(
                Zend_Queue::NAME => $this->queueName,
                'messageTableName' => 'vantage_message',
                'queueTableName' => 'vantage_queue',
                'driverOptions' => array(
                    'host' => $config->host,
                    'port' => $config->port,
                    'username' => $config->username,
                    'password' => $config->password,
                    'dbname' => $config->dbname,
                    'type' => 'pdo_mysql',
                    Zend_Queue::TIMEOUT => 1,
                    Zend_Queue::VISIBILITY_TIMEOUT => 1
                )
            );
            $zendDb = new VantageAnalytics_Analytics_Model_Queue_Adapter_Db($queueOptions);
            $this->registry[$this->queueName] = new Zend_Queue($zendDb, $queueOptions);
        }
        return $this->registry[$this->queueName];
    }

    protected function buildJob($msg)
    {
        $jobclass = $msg['class'];
        $classname = "VantageAnalytics_Analytics_Model_{$jobclass}";
        return new $classname;
    }

    protected function runJob($message)
    {
        $msg = json_decode($message->body, true);
        $job = $this->buildJob($msg);
        $method = $msg['method'];
        return call_user_func_array(array($job, $method), $msg['args']);
    }

    public function processQueue()
    {
        try {
            $maxLoops = 100;
            $queue = $this->getQueue();
            while (!$this->isEmpty()) {
                foreach ($queue->receive($this->maxBatchSize) as $message) {
                    $this->runJob($message);
                    $queue->deleteMessage($message);
                }
                // Stop once in a while to let other jobs run
                $maxLoops = $maxLoops - 1;
                if ($maxLoops < 1) {
                    return;
                }
            }
        } catch (Exception $e) {
            Mage::helper('analytics/log')->logException($e);
        }
    }

    public function isEmpty($queue)
    {
        return ($this->getQueue()->count() == 0);
    }

    public function enqueue($task)
    {
        $msg = json_encode($task);
        $this->getQueue()->send($msg);
        return true;
    }
}
