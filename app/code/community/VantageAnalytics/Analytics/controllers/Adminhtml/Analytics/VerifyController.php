<?php

class VantageAnalytics_Analytics_Adminhtml_Analytics_VerifyController extends Mage_Adminhtml_Controller_Action
{
    public function _isAllowed()
    {
        return true;
    }

    public function verifyAction()
    {
        $this->_redirectUrl(($this->_getRefererUrl()));

        $username = Mage::helper('analytics/account')->username();
        $secret = Mage::helper('analytics/account')->secret();
        if (empty($username) || empty($secret)) {
            Mage::getSingleton('adminhtml/session')
                ->init('core', 'adminhtml')
                ->addSuccess(
                    'Your Vantage Username or Secret is missing. ' .
                    'Enter them and click Save Config and Verify again.'
                );
        }

        try {
            $response = Mage::helper('analytics/account')->registerAccount(array("username" => $username, "secret" => $secret));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')
                ->init('core', 'adminhtml')
                ->addError($e->getMessage());
            return;
        }

        if (!empty($response) && array_key_exists("success", $response)) {
            Mage::helper('analytics/account')->setIsVerified(1);
            Mage::getSingleton('adminhtml/session')
                ->init('core', 'adminhtml')
                ->addSuccess('Your Vantage Account is verified.');
        } else {
            Mage::getSingleton('adminhtml/session')
                ->init('core', 'adminhtml')
                ->addError(
                    "Account Verfication with Vantage failed. " .
                    "Re-enter the VantageAnalytics username and secret, " .
                    "click Save Config and Verify again."
                );
        }
    }
}
