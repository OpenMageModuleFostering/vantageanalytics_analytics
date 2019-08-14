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

        $username = Mage::helper('analytics/account')->username();
        $post = Mage::app()->getRequest()->getPost();
        $username = $post['groups']['accountoptions']['fields']
            ['accountid']['value'];

        $vantageUrl = Mage::helper('analytics/account')->vantageUrl();

        $api = new VantageAnalytics_Analytics_Model_Api_Request(
            $vantageUrl, $username, $secret
        );
        $verifiyEntity = array('entity_type' => 'verification');

        try {
            $api->send('create', $verifiyEntity);
            Mage::helper('analytics/account')->setIsVerified(1);
            Mage::getSingleton('core/session')->addSuccess("Your VantageAnalytics "
                . "username and secret were verified."
            );
        } catch (VantageAnalytics_Analytics_Model_Api_Exception_BadRequest $e) {
            Mage::helper('analytics/account')->setIsVerified(0);
            Mage::throwException(
                "Verfication with VantageAnalytics.com failed! " .
                "Re-enter the VantageAnalytics username and secret. "
            );
        }

        return parent::save();
    }
}
