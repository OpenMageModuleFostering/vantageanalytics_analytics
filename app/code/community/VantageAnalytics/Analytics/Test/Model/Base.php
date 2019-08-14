<?php

abstract class VantageAnalytics_Analytics_Test_Model_Base extends EcomDev_PHPUnit_Test_Case
{
    public function setUp()
    {
        $tracking = $this->getMockBuilder('VantageAnalytics_Analytics_Model_Helper_Tracking')
            ->disableOriginalConstructor()->setMethods(array('setInitialCookie'))->getMock();
        $tracking->expects($this->any())->method('setInitialCookie');
        $this->replaceByMock('helper', 'analytics/tracking', $tracking);
    }
}
