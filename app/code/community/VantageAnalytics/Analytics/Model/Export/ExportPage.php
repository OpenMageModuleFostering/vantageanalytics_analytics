<?php

set_time_limit(0);
ini_set('memory_limit', -1);

require_once('app/Mage.php');

Mage::app();

$entityName = $argv[1];
$websiteId = $argv[2];
$startPage = (int)$argv[3];
$endPage = (int)$argv[4];

$exportClass = "VantageAnalytics_Analytics_Model_Export_" . $entityName;
$exporter = new $exportClass;
$exporter->exportPage($websiteId, $startPage, $endPage);

?>

