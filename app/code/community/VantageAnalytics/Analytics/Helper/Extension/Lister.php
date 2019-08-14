<?php
class VantageAnalytics_Analytics_Helper_Extension_Lister
{
    public static function factory()
    {
        return new self();
    }

    public function __construct()
    {
        $this->config = Mage::getConfig();
    }

    public function extensions()
    {
        return array_map("basename", $this->filePaths());
    }

    public function pools()
    {
        return array('core','community','local');
    }

    private function filePaths()
    {
        $extensions = array();

        foreach($this->pools() as $pool)
        {
            $extensions[] =
                VantageAnalytics_Analytics_Helper_Extension_Pool::factory($pool)
                    ->files();
        }

        return call_user_func_array('array_merge', $extensions);
    }

}