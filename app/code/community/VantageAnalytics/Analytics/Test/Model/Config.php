<?php

class VantageAnalytics_Analytics_Test_Model_Config extends VantageAnalytics_Analytics_Test_Model_Base
{
    /**
     * @test
     */
    public function configIsFoundAndHasExpectedStructure()
    {
        $config = Mage::getStoreConfig('vantageanalytics/accountoptions', Mage::app()->getStore());
        $this->assertTrue(array_key_exists('vantageurl', $config));
    }

}
