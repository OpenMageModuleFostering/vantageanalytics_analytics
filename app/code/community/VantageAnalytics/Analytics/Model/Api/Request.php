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


    protected function setupCurl($method, $entity)
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
        curl_setopt($channel, CURLOPT_CONNECTTIMEOUT_MS, 24000);

        $entity['username'] = $this->apiUsername;
        $body = json_encode($entity);
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
        if ($attempt == 5) {
            Mage::helper('analytics/log')->logError("Tried $attempt times, giving up");
            throw new VantageAnalytics_Analytics_Model_Api_Exceptions_MaxRetries("Maximum retries exceeded");
        }
        $waitTimes = array(5, 30, 60, 5*60, 10*60, 30*60, 60*60, 4*60*60);
        $seconds = $waitTimes[$attempt];
        Mage::helper('analytics/log')->logWarn("Waiting for {$seconds} seconds.");
        sleep($seconds);
    }

    protected function execCurl($method, $entity)
    {
        $reponse = null;
        $attempts = 0;
        $success = false;
        while ($attempts < 5 && !$success) {
            try {
                $response = $this->execCurlNoRetry($method, $entity);
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

    protected function execCurlNoRetry($method, $entity)
    {
        $channel = $this->setupCurl($method, $entity);
        $response = curl_exec($channel);
        $this->raiseOnError($channel);
        curl_close($channel);
        return $response;
    }

    protected function _send($entityMethod, $entity, $isExport)
    {
        $webhookFactory = VantageAnalytics_Analytics_Model_Api_Webhook::factory(
            $entity, $entityMethod, $isExport
        );
        $postData = $webhookFactory->getPostData();
        $this->execCurl("POST", $postData);
    }

    /*
     * Post the $entity data to vantage satellite app.
     */
    public function send($entityMethod, $entity)
    {
        $this->_send($entityMethod, $entity, false);
    }

    /*
     * Same as send but sets the isExport flag to skip some calculations that
     * don't need to run during imports.
     */ 
    public function export($entityMethod, $entity)
    {
        $this->_send($entityMethod, $entity, true); // true: isExport
    }

    /*
     * This is a more open ended version of send which does not assume method is POST
     * or the url is the base URL and allows the user to control retries.
     */
    public function request($method, $url, $data, $retryOnError=false)
    {
        $vantageUrl = $this->vantageUrl; // Save for later

        $this->vantageUrl = $url;
        if ($retryOnError) {
            $response = $this->execCurl($method, $data);
        } else {
            $response = $this->execCurlNoRetry($method, $data);
        }
        $this->vantageUrl = $vantageUrl; // Restore the object's state to be a good citizen
        return json_decode($response, true);
    }
}
