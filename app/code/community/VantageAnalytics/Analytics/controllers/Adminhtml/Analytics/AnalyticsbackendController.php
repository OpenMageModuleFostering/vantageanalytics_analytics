<?php

class VantageAnalytics_Analytics_Adminhtml_Analytics_AnalyticsbackendController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('analytics/adminhtml_analyticsbackend')
            ->_title($this->__("Vantage Analytics"));

        $this->renderLayout();
    }

    protected function collectStoreInfo()
    {
        $stores = array();

        foreach (Mage::app()->getWebsites() as $store) {
            $stores[] = array(
                'store_id' => $store->getId(),
                'domain' => $store->getDefaultStore()->getBaseUrl(),
                'name' => $store->getName()
            );
        }

        return $stores;
    }

    protected function registerAccount($params)
    {
        $registerUrl = Mage::helper("analytics/account")->registerAccountUrl();
        $channel = curl_init($registerUrl);

        curl_setopt($channel, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($channel, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($channel, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($channel, CURLOPT_CONNECTTIMEOUT_MS, 12200);
        curl_setopt($channel, CURLOPT_TIMEOUT_MS, 15000);

        $account = array(
            'username' => $params['username'],
            'password' => $params['password'],
            'password_confirmation' => $params['password_confirmation'],
            'stores' => $this->collectStoreInfo()
        );

        $body = json_encode($account);
        curl_setopt($channel, CURLOPT_POSTFIELDS, $body);
        $headers = array(
                'Content-type: application/json',
                'Content-length: ' . strlen($body)
            );


        curl_setopt($channel, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($channel);

        $status = curl_getinfo($channel, CURLINFO_HTTP_CODE);
        if ($status >= 500) {
                Mage::throwException("An error occurred. Please try again later.");
        }

        if (curl_errno($channel)) {
                $errorDesc = curl_error($channel);
                    curl_close($channel);
                    Mage::throwException("An error occurred. Please try again later.");
        }

        curl_close($channel);
        $response = json_decode($result, true);

        return $response;
    }

    protected function saveAccountLogin($response)
    {
        if (empty($response['username']) || empty($response['secret'])) {
            return;
        }

        Mage::helper('analytics/account')->setUsername($response['username']);
        Mage::helper('analytics/account')->setSecret($response['secret']);
        Mage::helper('analytics/account')->setIsVerified(1);
    }

    protected function handleValidation($response)
    {
        $fieldNames = array('username' => 'Email: ',
            'password' => 'Password: ',
            'password_confirmation' => 'Confirm Password: ',
            'non_field_errors' => ''
        );
        if (empty($response['errors'])) {
            Mage::getSingleton('adminhtml/session')
                ->init('core', 'adminhtml')
                ->addSuccess('Your Vantage Analytics account was created!');
        } else {
            foreach ($response['errors'] as $field => $errors) {
                foreach ($errors as $error) {
                    $fieldName = array_key_exists($field, $fieldNames) ?
                        $fieldNames[$field] : $field;
                    Mage::getSingleton('adminhtml/session')
                        ->init('core', 'adminhtml')
                        ->addError($fieldName . $error);
                }
            }
        }
    }

    public function registerAction()
    {
        try {
            $params = $this->getRequest()->getParams();
            $response = $this->registerAccount($params);
            $this->handleValidation($response);
            $this->saveAccountLogin($response);
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')
                ->init('core', 'adminhtml')
                ->addError($e->getMessage());
        }

        $this->loadLayout()
            ->_setActiveMenu('analytics/adminhtml_analyticsbackend')
            ->_title($this->__("Vantage Analytics"));

        $this->renderLayout();
    }
}
