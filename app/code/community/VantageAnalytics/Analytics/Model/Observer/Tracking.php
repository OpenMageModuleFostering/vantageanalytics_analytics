<?php

class VantageAnalytics_Analytics_Model_Observer_Tracking
{
    protected function isAdmin()
    {
        return (Mage::app()->getStore()->isAdmin() ||
                Mage::getDesign()->getArea() == 'adminhtml');
    }

    public function controllerFrontInitBefore($observer)
    {
        if(!Mage::helper('analytics/account')->isVerified()){
            return;
        }
        try {
            if ($this->isAdmin()) {
                return;
            }
            $frontController = $observer->getEvent()->getFront();
            $request = $frontController->getRequest();
            Mage::helper('analytics/tracking')->setInitialCookie($request);
        } catch (Exception $e) {
            Mage::helper('analytics/log')->logException($e);
        }
    }
}
