<?php

class VantageAnalytics_Analytics_Model_Resource_Mysql4_Setup extends Mage_Core_Model_Resource_Setup
{
    public function resetConfig()
    {
        Mage::helper('analytics/account')->setIsVerified(0);
    }
}
