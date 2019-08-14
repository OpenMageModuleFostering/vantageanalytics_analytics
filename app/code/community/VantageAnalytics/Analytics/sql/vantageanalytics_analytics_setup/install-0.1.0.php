<?php

$installer = $this;

$installer->startSetup();
$installer->run("
    DROP TABLE IF EXISTS `vantage_message`;
    CREATE TABLE `vantage_message` (
      `message_id` bigint(20) unsigned NOT NULL auto_increment,
      `queue_id` int(10) unsigned NOT NULL,
      `handle` char(32) default NULL,
      `body` mediumtext NOT NULL,
      `md5` char(32) NOT NULL,
      `timeout` decimal(14,4) unsigned default NULL,
      `created` int(10) unsigned NOT NULL,
      PRIMARY KEY  (`message_id`),
      UNIQUE KEY `message_handle` (`handle`),
      KEY `message_queueid` (`queue_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->run("
    DROP TABLE IF EXISTS `vantage_queue`;
    CREATE TABLE `vantage_queue` (
      `queue_id` int(10) unsigned NOT NULL auto_increment,
      `queue_name` varchar(100) NOT NULL,
      `timeout` smallint(5) unsigned NOT NULL default '30',
      PRIMARY KEY  (`queue_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->run("
    ALTER TABLE `vantage_message`
    ADD CONSTRAINT fk_vantage_queue_id
        FOREIGN KEY (`queue_id`) REFERENCES `vantage_queue` (`queue_id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE;
");

$installer->endSetup();
