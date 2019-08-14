<?php

class VantageAnalytics_Analytics_Test_Model_Api_Signature extends VantageAnalytics_Analytics_Test_Model_Base
{
    /**
     * @test
     */
    public function sign()
    {
        $signer = new VantageAnalytics_Analytics_Model_Api_Signature('', '');
        $this->assertEquals(
            "b613679a0814d9ec772f95d778c35fc5ff1697c493715653c6c712144292c5ad",
            $signer->sign()
        );
    }

    /**
     * @test
     */
    public function signatureHeader()
    {
        $signer = new VantageAnalytics_Analytics_Model_Api_Signature('', '');
        $this->assertEquals(
            "X-Vantage-Hmac-SHA256: b613679a0814d9ec772f95d778c35fc5ff1697c493715653c6c712144292c5ad",
            $signer->signatureHeader()
        );
    }
}
