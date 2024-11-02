<?php

namespace Byjuno\ByjunoPayments\Api;

class CembraPayAuthorization
{
    public $authorizationValidTill; //String
    public $authorizedRemainingAmount; //int
    public $authorizationCurrency; //String
}
class CembraPayTransactionStatus
{
    public $transactionId; //String
    public $transactionStatus; //int
    public $transactionMessages; //Array
}

class CembraPayGetStatusResponse
{
    public $requestMerchantId; //String
    public $requestMsgType; //boolean
    public $requestMsgId; //String
    public $requestMsgDateTime; //String
    public $replyMsgId; //String
    public $replyMsgDateTime; //String
    public $token; //String
    public $merchantCustRef; //String
    public $isTokenDeleted; //String
    public $merchantOrderRef; //String
    public $processingStatus; //String
    public $authorization; //String
    public $transactionStatus; //String

    public function __construct()
    {
        $this->authorization = new CembraPayAuthorization();
        $this->transactionStatus = new CembraPayTransactionStatus();
    }
}
