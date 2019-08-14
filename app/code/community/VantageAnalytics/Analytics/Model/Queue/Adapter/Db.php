<?php

class VantageAnalytics_Analytics_Model_Queue_Adapter_Db extends Zend_Queue_Adapter_Db
{
    public function __construct($options, Zend_Queue $queue = null)
    {
        parent::__construct($options, $queue);
        if (isset($options['queueTableName'])) {
            $this->_queueTable->setOptions(
                array(Zend_Db_Table_Abstract::NAME => $options['queueTableName'])
            );
        }
        if (isset($options['messageTableName'])) {
            $this->_messageTable->setOptions(
                array(Zend_Db_Table_Abstract::NAME => $options['messageTableName'])
            );
        }
    }
}
