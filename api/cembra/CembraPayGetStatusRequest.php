<?php

namespace Byjuno\ByjunoPayments\Api;

class CembraPayGetStatusRequest
{
    public $requestMsgType; //boolean
    public $requestMsgId; //String
    public $requestMsgDateTime; //String
    public $transactionId; //String

    public function createRequest() {
        return json_encode($this);
    }
}
