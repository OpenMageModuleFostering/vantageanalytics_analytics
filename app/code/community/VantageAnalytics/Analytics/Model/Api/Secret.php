<?php

class VantageAnalytics_Analytics_Model_Api_Secret extends Mage_Core_Model_Config_Data
{
    public function save()
    {
        $secret = $this->getValue();
        if (strlen($secret) == 0) {
            Mage::throwException("The VantageAnalytics Secret is required.");
        }
        if (strlen($secret) < 32) {
            Mage::throwException(
                "Double check the VantageAnalytics Secret, it " .
                " should be 32 characters long."
            );
        }

        return parent::save();
    }
}
