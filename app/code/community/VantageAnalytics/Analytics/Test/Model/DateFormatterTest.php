<?php

class VantageAnalytics_Analytics_Test_Model_DateFormatterTest extends VantageAnalytics_Analytics_Test_Model_Base
{
    /**
     * @test
     */
    public function test_utc_date_convrsion()
    {
        $created_at = '2015-01-05T15:31:04-05:00';

        $utcDate = VantageAnalytics_Analytics_Helper_DateFormatter::factory($created_at)->toUtcDate();

        $this->assertEquals("2015-01-05T20:31:04+00:00", $utcDate);
    }

    /**
     * @test
     */
    public function test_utc_dat_formatting()
    {
        $updated_at = '2015-01-05 20:31:04';

        $utcDate = VantageAnalytics_Analytics_Helper_DateFormatter::factory($updated_at)->toUtcDate();

        $this->assertEquals("2015-01-05T20:31:04+00:00", $utcDate);
    }
}
