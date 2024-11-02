<?php

namespace Byjuno\ByjunoPayments\Api;

class CembraPayCheckoutSettleRequest extends CembraPayCheckoutAutRequest
{
    public $requestMsgType; //String
    public $requestMsgId; //String
    public $requestMsgDateTime; //Date
    public $merchantOrderRef; //String
    public $amount; //int
    public $currency; //String
    public $settlementDetails; //seliveryDetails
    public $deliveryDetails; //DeliveryDetails
    public $transactionId;

    public function __construct()
    {
        $this->deliveryDetails = new DeliveryDetails();
        $this->settlementDetails = new SettlementDetails();
    }

    public function createRequest() {
        return json_encode($this);
    }
}
