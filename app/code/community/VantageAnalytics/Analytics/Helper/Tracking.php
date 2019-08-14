<?php

class VantageAnalytics_Analytics_Helper_Tracking extends Mage_Core_Helper_Abstract
{
    const COOKIE_NAME = '__vantage';

    public function gatherTrackingData($request)
    {
        $tracking = array(
            'landing_site' => $request->getRequestUri(),
            'referral_site' => $request->getServer('HTTP_REFERER')
        );
        return $tracking;
    }

    public function setInitialCookie($request)
    {
        if ($this->hasTrackingCookie()) {
            return;
        }
        $tracking = $this->gatherTrackingData($request);
        $cookie = json_encode($tracking);
        $sixtyDays = 86400 * 60;
        Mage::getModel('core/cookie')->set(self::COOKIE_NAME, $cookie, $sixtyDays);
    }

    public function hasTrackingCookie()
    {
        $currentCookie = $this->getTrackingFromCookie();
        return (count($currentCookie) > 0);
    }

    public function getTrackingFromCookie()
    {
        $cookie = Mage::getModel('core/cookie')->get(self::COOKIE_NAME);
        $tracking = json_decode($cookie, true);
        if (!is_null($tracking)) {
            return $tracking;
        }
        return array();
    }

}
