<?php

class VantageAnalytics_Analytics_ExportController extends Mage_Core_Controller_Front_Action
{
    protected function _verifySignature()
    {
        $requestSignature = $this->getRequest()->getParam('signature');

        if (empty($requestSignature)) {
            $this->_setResponse(
                array("failure" => "Invalid request."), '401'
            );
            return false;
        }

        $params = $this->getRequest()->getParams();
        ksort($params);
        unset($params['signature']);
        $query = http_build_query($params);

        $secret = Mage::helper('analytics/account')->secret();
        $mySignature = base64_encode(hash_hmac("sha256", $query, $secret, true));

        if (!Mage::helper('analytics/account')->hashEquals($mySignature, $requestSignature)) {
            $this->_setResponse(
                array("failure" => "Invalid request."), '403'
            );
            return false;
        }

        return true;
    }

    public function storesAction()
    {
        if (!$this->_verifySignature()) {
            return;
        }

        $websites = Mage::app()->getWebsites();

        $inlineApi = new VantageAnalytics_Analytics_Model_Api_Inline();

        // The first argument is pageSize and is unused here
        $exporter = new VantageAnalytics_Analytics_Model_Export_Store(5000, $inlineApi);

        foreach ($websites as $website) {
            $exporter->exportWebsite($website);
        }

        $results = $inlineApi->queue;

        $this->_setResponse(
            array(
                "model" => 'Store',
                "currentPage" => 1,
                "totalPages" => 1,
                "pageSize" => count($websites),
                "totalItems" => count($websites),
                "results" => $results,
            )
        );
    }

    public function ordersAction()
    {
        return $this->_pageAction('Order');
    }

    public function productsAction()
    {
        return $this->_pageAction('Product');
    }

    public function customersAction()
    {
        return $this->_pageAction('Customer');
    }

    protected function _setResponse($data, $status='200')
    {
        $this->getResponse()
            ->setHeader('HTTP/1.0', $status, true)
            ->setHeader('Content-Type', 'application/json', true)
            ->appendBody(json_encode($data));
    }

    protected function _pageAction($entityName)
    {
        if (!$this->_verifySignature()) {
            return;
        }

        if (!in_array($entityName, array('Product', 'Customer', 'Order'))) {
            $this->_setResponse(
                array("failure" => "Invalid model provided"), '400'
            );
            return;
        }

        $currentPage = max((int) $this->getRequest()->getParam('page', 1), 0);
        $pageSize = min((int) $this->getRequest()->getParam('page_size', 50), 250);
        $websiteId = $this->getRequest()->getParam('store_id', 1);
        $noResults = $this->getRequest()->getParam('no_results', 0);

        $exportClass = "VantageAnalytics_Analytics_Model_Export_" . $entityName;
        $inlineApi = new VantageAnalytics_Analytics_Model_Api_Inline();

        $exporter = new $exportClass($pageSize, $inlineApi);

        if ($noResults) {
            // Only export the metadata (page size, page count, item count)
            $exporter->exportMetaData($websiteId);
        } else {
            // Export metadata and results
            $exporter->exportPage($websiteId, $currentPage, $currentPage + 1);
        }

        $results = $inlineApi->queue;
        $metaData = $inlineApi->metaData;

        $this->_setResponse(
            array(
                "model" => $entityName,
                "currentPage" => $currentPage,
                "totalPages" => $metaData['total_pages'],
                "pageSize" => $pageSize,
                "totalItems" => $metaData['total_items'],
                "results" => $results,
            ),
            '200'
        );
    }

    public function finalizeAction()
    {
        if (!$this->_verifySignature()) {
            return;
        }

        Mage::helper('analytics/account')->setExportDone(1);
        Mage::helper('analytics/account')->setIsVerified(1);

        $this->_setResponse(
            array(
                'isExportDone' => Mage::helper('analytics/account')->isExportDone(),
                'isVerified' => Mage::helper('analytics/account')->isVerified()
            ),
            200
        );
    }

    public function queueAction()
    {
        if (!$this->_verifySignature()) {
            return;
        }

        $batchSize = (int) $this->getRequest()->getParam('batch_size', 20);

        $messages = array();
        $queue = Mage::helper('analytics/queue');
        foreach ($queue->receiveMessages($batchSize) as $message) {
            $msg = json_decode($message->body, true);
            $messages[] = $msg;
            $queue->deleteMessage($message);
        }

        $this->_setResponse(
            array(
                'results' => $messages,
                'queueSize' => $queue->getMessageCount(),
            ),
            '200'
        );
    }

    public function pixelAction()
    {
        if (!$this->_verifySignature()) {
            return;
        }

        $storeId = (int) $this->getRequest()->getParam('store_id');
        $url = $this->getRequest()->getParam('url');

        $store = Mage::app()->getStore($storeId);
        if ($store) {
            $config = Mage::app()->getConfig();
            $config->saveConfig('vantageanalytics/trackingpixel/url', $url, 'stores', $storeId);
            $this->_setResponse(array('success' => 'store pixel url updated'), '200');
        } else {
            $this->_setResponse(array('failure' => 'store not found'), '400');
        }
    }

    public function infoAction()
    {
        if (!$this->_verifySignature()) {
            return;
        }

        $data = VantageAnalytics_Analytics_Model_Debug::factory()->toVantage();

        $this->_setResponse($data, '200');
    }

    public function cronAction()
    {
        if (!$this->_verifySignature()) {
            return;
        }

        $enable = $this->getRequest()->getParam('enable', 0);
        $disable = $this->getRequest()->getParam('disable', 0);

        if ($enable) {
            Mage::helper('analytics/account')->setCronEnabled(1);
        }

        if ($disable) {
            Mage::helper('analytics/account')->setCronEnabled(0);
        }

        $this->_setResponse(
            array(
                'cronEnabled' => Mage::helper('analytics/account')->isCronEnabled()
            ),
            '200'
        );
    }
}
