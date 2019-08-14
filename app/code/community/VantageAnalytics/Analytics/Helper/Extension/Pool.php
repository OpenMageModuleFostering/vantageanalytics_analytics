<?php
class VantageAnalytics_Analytics_Helper_Extension_Pool
{
    public static function factory($poolName)
    {
        return new self($poolName);
    }

    public function __construct($poolName)
    {
        $this->poolName = $poolName;
        $this->config = Mage::getConfig();
    }

    public function files()
    {
        $final = array();

        foreach ($this->_files() as $file)
        {
            if(is_dir($file))
            {
                $final[] = $file;
            }
        }
        return $final;
    }

    private function _files()
    {
        return glob($this->_poolPath($this->poolName) . DS . '*');
    }

    private function _basePath()
    {
        return $this->config->getOptions()->getCodeDir();
    }

    private function _poolPath($pool)
    {
        return $this->_basePath() . DS . $pool;
    }
}

