<?php

class VantageAnalytics_Analytics_Block_Adminhtml_Analyticsbackend extends Mage_Adminhtml_Block_Template {

    public function isAccountVerified()
    {
        return Mage::helper('analytics/account')->isVerified();
    }
}
