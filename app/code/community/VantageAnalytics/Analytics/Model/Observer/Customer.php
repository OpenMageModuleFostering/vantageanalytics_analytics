<?php
class VantageAnalytics_Analytics_Model_Observer_Customer extends VantageAnalytics_Analytics_Model_Observer_Base
{
    public function __construct()
    {
        parent::__construct('Customer');
    }

    protected function getEntity($event)
    {
        return $event->getCustomer();
    }

    public function customerRegisterSuccess($observer)
    {
        $this->performSave($observer);
    }

    public function customerSaveAfter($observer)
    {
        $this->performSave($observer);
    }

    public function customerDeleteAfter($observer)
    {
        $this->performDelete($observer);
    }
}

