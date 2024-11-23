<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_0_0($module)
{
    Db::getInstance()->Execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'cembra_logs` (
                  `cembra_id` int(10) unsigned NOT NULL auto_increment,
                  `request_id` varchar(250) default NULL,
                  `request_type` varchar(250) default NULL,
                  `firstname` varchar(250) default NULL,
                  `lastname` varchar(250) default NULL,
                  `town` varchar(250) default NULL,
                  `postcode` varchar(250) default NULL,
                  `street` varchar(250) default NULL,
                  `country` varchar(250) default NULL,
                  `ip` varchar(250) default NULL,
                  `cembra_status` varchar(250) default NULL,
                  `order_id` varchar(250) default NULL,
                  `transaction_id` varchar(250) default NULL,
                  `request` text default NULL,
                  `response` text default NULL,
                  `creation_date` TIMESTAMP NULL DEFAULT now() ,
                  PRIMARY KEY  (`cembra_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
    return true;
}
