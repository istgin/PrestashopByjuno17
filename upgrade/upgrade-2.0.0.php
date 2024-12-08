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
                  `custom_field` text default NULL,
                  `creation_date` TIMESTAMP NULL DEFAULT now() ,
                  PRIMARY KEY  (`cembra_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

    Configuration::updateValue('BYJUNO_TOC_INVOICE_EN', 'https://cembrapay.ch/en/terms/CP');
    Configuration::updateValue('BYJUNO_TOC_INSTALLMENT_EN', 'https://cembrapay.ch/en/terms');
    Configuration::updateValue('BYJUNO_TOC_INVOICE_DE', 'https://cembrapay.ch/de/terms/CP');
    Configuration::updateValue('BYJUNO_TOC_INSTALLMENT_DE', 'https://cembrapay.ch/de/terms');
    Configuration::updateValue('BYJUNO_TOC_INVOICE_FR', 'https://cembrapay.ch/fr/terms/CP');
    Configuration::updateValue('BYJUNO_TOC_INSTALLMENT_FR', 'https://cembrapay.ch/fr/terms');
    Configuration::updateValue('BYJUNO_TOC_INVOICE_IT', 'https://cembrapay.ch/it/terms/CP');
    Configuration::updateValue('BYJUNO_TOC_INSTALLMENT_IT', 'https://cembrapay.ch/it/terms');

    return true;
}
