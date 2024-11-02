<?php

namespace Byjuno\ByjunoPayments\Api;

class CembraPayCheckoutCreditRequest extends CembraPayCheckoutAutRequest
{
    public $requestMsgType; //String
    public $requestMsgId; //String
    public $requestMsgDateTime; //Date
    public $merchantOrderRef; //String
    public $amount; //int
    public $currency; //String
    public $settlementDetails; //seliveryDetails
    public $transactionId;

    public function __construct()
    {
        $this->deliveryDetails = new DeliveryDetails();
        $this->settlementDetails = new SettlementDetailsMemo();
    }

    public function createRequest() {
        return json_encode($this);
    }
}
