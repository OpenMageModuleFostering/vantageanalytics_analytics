<?php

class VantageAnalytics_Analytics_Model_Api_Username extends Mage_Core_Model_Config_Data
{
    public function save()
    {
        $username = $this->getValue();
        if (strlen($username) == 0) {
            Mage::throwException("Your VantageAnalytics username is required");
        }
        return parent::save();
    }
}
