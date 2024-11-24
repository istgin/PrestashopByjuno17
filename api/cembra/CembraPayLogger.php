<?php

namespace Byjuno\ByjunoPayments\Api;

use Db;

class CembraPayLogger
{
    private static $instance = NULL;
    private $logs;

    private function __construct() {
        $this->logs = array();
    }

    public static function getInstance() {
        if(self::$instance === NULL) {
            self::$instance = new CembraPayLogger();
        }
        return self::$instance;
    }

    public function saveCembraLog($request, $response, $status, $type,
                                   $firstName, $lastName, $requestId,
                                   $postcode, $town, $country, $street1, $transactionId, $orderId)
    {
        $json_string1 = json_decode($request);
        if ($json_string1 == null) {
            $json_string11 = $request;
        } else {
            $json_string11 = json_encode($json_string1, JSON_PRETTY_PRINT);
        }
        $json_string2 = json_decode($response);
        if ($json_string2 == null) {
            $json_string22 = $response;
        } else {
            $json_string22 = json_encode($json_string2, JSON_PRETTY_PRINT);
        }
        if (empty($json_string11)) {
            $json_string11 = "no request";
        }
        if (empty($json_string22)) {
            $json_string22 = "no response";
        }

        $sql = '
                INSERT INTO `'._DB_PREFIX_.'cembra_logs` (
                  `request_id`,
                  `request_type`,
                  `firstname`,
                  `lastname`,
                  `town`,
                  `postcode`,
                  `street`,
                  `country`,
                  `ip`,
                  `cembra_status` ,
                  `order_id`,
                  `transaction_id`,
                  `request`,
                  `response`
                )
                VALUES
                (
                    \''.pSQL((string)$requestId).'\',
                    \''.pSQL((string)$type).'\',
                    \''.pSQL((string)$firstName).'\',
                    \''.pSQL((string)$lastName).'\',
                    \''.pSQL((string)$town).'\',
                    \''.pSQL((string)$postcode,).'\',
                    \''.pSQL((string)$street1).'\',
                    \''.pSQL((string)$country).'\',
                    \''.pSQL(empty($_SERVER['REMOTE_ADDR']) ? "no ip" : $_SERVER['REMOTE_ADDR']).'\',
                    \''.pSQL((string)$status).'\',
                    \''.pSQL((string)$orderId).'\',
                    \''.pSQL((string)$transactionId).'\',
                    \''.pSQL((string)$json_string11).'\',
                    \''.pSQL((string)$json_string22).'\'
                )
        ';
        Db::getInstance()->Execute($sql);
    }
};
