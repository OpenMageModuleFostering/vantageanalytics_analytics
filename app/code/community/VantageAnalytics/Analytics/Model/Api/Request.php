<?php

class VantageAnalytics_Analytics_Model_Api_Request
{
    public function __construct($vantageUrl=null, $username=null, $secret=null)
    {
        $this->vantageUrl = $vantageUrl ? $vantageUrl : Mage::helper("analytics/account")->vantageUrl();
        $this->apiUsername = $username ? $username : Mage::helper("analytics/account")->username();
        $this->apiSecret = $secret ? $secret : Mage::helper("analytics/account")->secret();
    }

    protected function makeSignatureHeader($body)
    {
        $signer = new VantageAnalytics_Analytics_Model_Api_Signature($body, $this->apiSecret);
        return $signer->signatureHeader();
    }


    protected function setupCurl($method, $entityData)
    {
        $uri = "{$this->vantageUrl}/";
        $channel = curl_init("$uri");
        if (!$channel) {
            $errorDesc = curl_error($channel);
            throw new VantageAnalytics_Analytics_Model_Api_Exceptions_CurlError(
                $errorDesc
            );
        }
        curl_setopt($channel, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($channel, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($channel, CURLOPT_RETURNTRANSFER, true);

        $entityData['username'] = $this->apiUsername;
        $body = json_encode($entityData);
        curl_setopt($channel, CURLOPT_POSTFIELDS, $body);
        $headers = array(
            'Content-type: application/json',
            'Content-length: ' . strlen($body),
            $this->makeSignatureHeader($body)
        );
        curl_setopt($channel, CURLOPT_HTTPHEADER, $headers);

        return $channel;
    }

    protected function raiseOnError($channel)
    {
        $status = curl_getinfo($channel, CURLINFO_HTTP_CODE);

        if ($status >= 400 && $status < 500) {
            throw new VantageAnalytics_Analytics_Model_Api_Exceptions_BadRequest;
        }
        if ($status >= 500) {
            throw new VantageAnalytics_Analytics_Model_Api_Exceptions_ServerError;
        }

        if (curl_errno($channel)) {
            $errorDesc = curl_error($channel);
            curl_close($channel);
            throw new VantageAnalytics_Analytics_Model_Api_Exceptions_CurlError(
                $errorDesc
            );
        }
    }

    protected function wait($attempt)
    {
        if ($attempt == 10) {
            Mage::helper('analytics/log')->logError("Tried $attempt times, giving up");
            throw new VantageAnalytics_Analytics_Model_Api_Exceptions_MaxRetries("Maximum retries exceeded");
        }
        $waitTimes = array(30, 60, 5*60, 10*60, 30*60, 60*60,
            2*60*60, 5*60*60, 12*60*60, 24*60*60);
        $seconds = $waitTimes[$attempt];
        Mage::helper('analytics/log')->logWarn("Waiting for {$seconds} seconds.");
        sleep($seconds);
    }

    protected function execCurl($method, $entityData)
    {
        $attempts = 0;
        $success = false;
        while ($attempts < 10 && !$success) {
            try {
                $channel = $this->setupCurl($method, $entityData);
                $response = curl_exec($channel);
                $this->raiseOnError($channel);
                curl_close($channel);
                $success = true;
            } catch (VantageAnalytics_Analytics_Model_Api_Exceptions_ServerError $e) {
                $this->wait($attempts);
            } catch (VantageAnalytics_Analytics_Model_Api_Exceptions_CurlError $e) {
                $this->wait($attempts);
            } catch (VantageAnalytics_Analytics_Model_Api_Exceptions_BadRequest $e) {
                $this->wait($attempts);
            }
            $attempts += 1;
        }
        return $response;
    }

    public function send($method, $entity)
    {
        $postData = VantageAnalytics_Analytics_Model_Api_Webhook::factory(
            $entity, $method
        )->getPostData();
        $this->execCurl("POST", $postData);
    }
}
