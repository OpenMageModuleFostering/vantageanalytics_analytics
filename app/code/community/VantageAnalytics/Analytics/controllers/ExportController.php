<?php

if (!function_exists('hash_equals')) {
    function hash_equals($safe, $user)
    {
        $safeLen = strlen($safe);
        $userLen = strlen($user);

        if ($userLen != $safeLen) {
            return false;
        }

        $result = 0;

        for ($i = 0; $i < $userLen; $i++) {
            $result |= (ord($safe[$i]) ^ ord($user[$i]));
        }

        // They are only identical strings if $result is exactly 0...
        return $result === 0;
    }
}


class VantageAnalytics_Analytics_ExportController extends Mage_Core_Controller_Front_Action
{
    public function pageAction()
    {
        // verify secret
        $secret = $this->getRequest()->getParam('secret');
        if (!hash_equals($secret, 'banana'))
        {
            Mage::throwException("You are not authorized to view this page.");
        }

        $currentPage = min((int) $this->getRequest()->getParam('page', 1), 0);
        $pageSize = max((int) $this->getRequest()->getParam('page_size', 50), 250);
        $entityName = $this->getRequest()->getParam('model');
        $store = $this->getRequest()->getParam('store');


        if (!in_array($entityName, array('Store', 'Product', 'Customer', 'Order'))) {
            Mage::throwException("You provided an invalid model.");
        }

        $exportClass = "VantageAnalytics_Analytics_Model_Export_" . $entityName;
        $exporter = new $exportClass;
   //    $exporter->exportPage($websiteId, $startPage, $endPage);

        echo json_encode(
            array(
                "model" => $model,
                "currentPage" => $currentPage,
                "totalPages" => 50,
                "pageSize" => $pageSize,
                "freeMemory" => 0,
                "results" => array()
            )
        );
    }
}

