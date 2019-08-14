<?php

abstract class VantageAnalytics_Analytics_Model_Export_Base
{
    const PAGE_SIZE = 500;

    abstract protected function createCollection($website, $pageNumber);

    protected function tryToFindPHP($phpv)
    {
        if (file_exists("/usr/local/php{$phpv}/bin/php")) {
            return "/usr/local/php{$phpv}/bin/php";
        }

        if (file_exists("/usr/local/php{$phpv}/bin/php{$phpv}")) {
            return "/usr/local/php{$phpv}/bin/php{$phpv}";
        }

        if (file_exists("/usr/bin/php${phpv}")) {
            return "/usr/bin/php{$phpv}";
        }

        if (file_exists("/usr/local/bin/php{$phpv}")) {
            return "/usr/local/bin/php{$phpv}";
        }

        if (function_exists("exec")) {
            $exe = exec("which php{$phpv}");
            if ($exe) {
                return $exe;
            }
        }
    }

    protected function getPathToPHP()
    {
        if (defined("PHP_BINARY") && file_exists(PHP_BINARY)) {
            return PHP_BINARY;
        }

        // mage> echo " " . getmypid() . " " . posix_getpid() . " " . exec('echo $PPID');
        //  31728 31728 31728
        if (function_exists('posix_getpid')) {
            $pid = posix_getpid();
        } else if (function_exists('getmypid')) {
            $pid = getmypid();
        } else {
            $pid = exec('echo $PPID');
        }

        if (function_exists('exec')) {
            $exe = exec("readlink -f /proc/$pid/exe");
            if ($exe && file_exists($exe)) {
                return $exe;
            }
        }

        $exe = $this->tryToFindPHP("56");
        if ($exe) {
            return $exe;
        }
        $exe = $this->tryToFindPHP("5");
        if ($exe) {
            return $exe;
        }
        $exe = $this->tryToFindPHP("");
        if ($exe) {
            return $exe;
        }
    }

    public function __construct($pageSize = null, $api=null, $transformer='')
    {
        $this->transformer = $transformer;
        $this->pageSize = !is_null($pageSize) ? $pageSize : self::PAGE_SIZE;
        $this->api = (!is_null($api) ? $api :
            new VantageAnalytics_Analytics_Model_Api_RequestQueue());
    }

    protected function makeTransformer($entity)
    {
        return Mage::getModel("analytics/Transformer_{$this->transformer}", $entity);
    }

    protected function enqueue($data)
    {
        $this->api->enqueue('create', $data, true);
    }

    protected function getEntityName()
    {
        if ($this->transformer == 'SalesOrder') {
            return 'Order';
        } else {
            return $this->transformer;
        }
    }

    protected function exportEntity($entity)
    {
        $transformer = $this->makeTransformer($entity);
        $data = $transformer->toVantage();
        $this->enqueue($data);
    }

    protected function exportMetaData($websiteId, $entity, $currentPage, $totalPages, $pageSize)
    {
        $this->api->enqueue(
            'progress',
            array(
                'store_ids' => array($websiteId),
                'entity_type' => $entity,
                'current_page' => $currentPage,
                'total_pages' => $totalPages,
                'page_size' => $pageSize
            ),
            true
        );
    }

    protected function exportWebsite($website)
    {
        $numberOfPages = 5;
        $websiteId = $website->getWebsiteId();
        $collection = $this->createCollection($website, null);
        if (is_null($collection)) {
            return;
        }
        $totalPages = $collection->getLastPageNumber();
        $entityName = $this->getEntityName();
        $currentPage = 0;
        $endPage = $currentPage + $numberOfPages;
        $where = Mage::getBaseDir('code') . '/community/VantageAnalytics/Analytics/Model/Export';
        $phpbin = $this->getPathToPHP();

        $pids = array();
        $maxChildren = 1;

        while ($currentPage <= $totalPages) {

            $this->exportMetaData(
                $website->getWebsiteId(),
                strtolower($entityName),
                $currentPage,
                $totalPages,
                $this->pageSize
            );

            if ($endPage >= $totalPages) {
                $endPage = $totalPages + 1; // The page ranges are inclusive-exclusive so pick up the last page
            }
            $args = array("{$where}/ExportPage.php", $entityName, "{$websiteId}", "{$currentPage}", "{$endPage}");
            if (function_exists('pcntl_fork')) {
                $pid = pcntl_fork();
                if ($pid == -1) {
                    Mage::helper('analytics/log')->logError('Could not fork');
                    die('could not fork');
                } else if ($pid) {
                    // in the parent
                    $pids[$pid] = $pid;

                    $endPage = $endPage + $numberOfPages;
                    $currentPage = $currentPage + $numberOfPages;

                } else {
                    // in the child
                    pcntl_exec($phpbin, $args);
                }

                if (count($pids) >= $maxChildren) {
                    $pid = pcntl_waitpid(-1, $status);
                    unset($pids[$pid]);
                }

            } else {
                $this->exportPage($websiteId, $currentPage, $endPage);

                $endPage = $endPage + $numberOfPages;
                $currentPage = $currentPage + $numberOfPages;
            }
        }
    }

    public function run()
    {
        $websites = Mage::app()->getWebsites();
        foreach ($websites as $website) {
           $this->exportWebsite($website);
        }
    }

    public function exportPage($websiteId, $startPage, $endPage)
    {
        $websites = Mage::app()->getWebsites();
        foreach ($websites as $website) {
            if ($websiteId == $website->getWebsiteId()) {
                $currentPage = $startPage;
                while ($currentPage < $endPage) {
                    $collection = $this->createCollection($website, $currentPage);

                    foreach ($collection as $entity) {
                        $this->exportEntity($entity);
                        try {
                            $entity->clearInstance();
                        } catch (Exception $e) {
                            Mage::helper('analytics/log')->logException($e);
                        }
                    }

                    Mage::helper('analytics/log')->logInfo("Completed page ${currentPage}");

                    $this->processQueue();

                    $currentPage++;

                    $collection->clear();
                }
            }
        }
    }

    protected function processQueue()
    {
        $queue = Mage::helper('analytics/queue');
        $queue->processQueue();
    }
}
