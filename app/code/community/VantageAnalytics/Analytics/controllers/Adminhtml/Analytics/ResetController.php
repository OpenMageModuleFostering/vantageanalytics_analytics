<?php

class VantageAnalytics_Analytics_Adminhtml_Analytics_ResetController extends Mage_Adminhtml_Controller_Action
{
    private function resetConfig()
    {
        Mage::helper('analytics/log')->logInfo("Resetting config...");
        Mage::helper('analytics/account')->setIsVerified(0);
        Mage::helper('analytics/account')->setExportDone(0);
        Mage::app()->reinitStores();
    }

    private function emptyQueue()
    {
        Mage::helper('analytics/log')->logInfo("Emptying message queue...");
        $db = Mage::getSingleton('core/resource')->getConnection('core_write');
        $db->query("TRUNCATE `vantage_message`;");
    }

    public function resetAction()
    {
        $this->emptyQueue();
        $this->resetConfig();
        Mage::getSingleton('adminhtml/session')
            ->init('core', 'adminhtml')
            ->addSuccess('Your Vantage settings were reset. Please re-enter your username and secret.');

        $this->_redirectUrl(($this->_getRefererUrl()));
    }
}
