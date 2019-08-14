<?php

class VantageAnalytics_Analytics_Test_Model_Webhook extends VantageAnalytics_Analytics_Test_Model_Base
{
    /**
     * @test
     * @doNotIndexAll
     */
    public function webhookType()
    {
        $webhook =
            VantageAnalytics_Analytics_Model_Api_Webhook::factory(array(), "update")->preamble();

        $this->assertEquals("object.update", $webhook['webhook']['type']);
    }

}
