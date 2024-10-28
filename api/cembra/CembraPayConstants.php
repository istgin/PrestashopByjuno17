<?php

class CembraPayConstants
{
    public static $SINGLEINVOICE = 'SINGLE-INVOICE';
    public static $CEMBRAPAYINVOICE = 'CEMBRAPAY-INVOICE';

    public static $INSTALLMENT_3 = 'INSTALLMENT_3';
    public static $INSTALLMENT_4 = 'INSTALLMENT_4';
    public static $INSTALLMENT_6 = 'INSTALLMENT_6';
    public static $INSTALLMENT_12 = 'INSTALLMENT_12';
    public static $INSTALLMENT_24 = 'INSTALLMENT_24';
    public static $INSTALLMENT_36 = 'INSTALLMENT_36';
    public static $INSTALLMENT_48 = 'INSTALLMENT_48';

    public static $MESSAGE_SCREENING = 'SCR';
    public static $MESSAGE_AUTH = 'AUT';
    public static $MESSAGE_SET = 'SET';
    public static $MESSAGE_CNL = 'CNT';
    public static $MESSAGE_CAN = 'CAN';
    public static $MESSAGE_CHK = 'CHK';
    public static $MESSAGE_STATUS = 'TST';

    public static $CUSTOMER_PRIVATE = 'P';
    public static $CUSTOMER_BUSINESS = 'C';


    public static $GENTER_UNKNOWN = 'N';
    public static $GENTER_MALE = 'M';
    public static $GENTER_FEMALE = 'F';


    public static $DELIVERY_POST = 'POST';
    public static $DELIVERY_VIRTUAL = 'DIGITAL';

    public static $SCREENING_OK = 'SCREENING-APPROVED';

    public static $SETTLE_OK = 'SETTLED';
    public static $SETTLE_STATUSES = ['SETTLED', 'PARTIALLY-SETTLED'];

    public static $AUTH_OK = 'AUTHORIZED';
    public static $CREDIT_OK = 'SUCCESS';
    public static $CANCEL_OK = 'SUCCESS';
    public static $CHK_OK = 'SUCCESS';
    public static $GET_OK = 'SUCCESS';
    public static $GET_OK_TRANSACTION_STATUSES = ['AUTHORIZED', 'SETTLED', 'PARTIALLY SETTLED'];
    public static $CNF_OK = 'SUCCESS';
    public static $CNF_OK_TRANSACTION_STATUSES = ['AUTHORIZED', 'SETTLED', 'PARTIALLY SETTLED'];


    public static $REQUEST_ERROR = 'REQUEST_ERROR';

    public static $allowedCembraPayPaymentMethods;

    public static $tokenSeparator = "||||";

    static function screeningResponse($response)
    {
        $responseObject = json_decode($response);
        $result = new CembraPayCheckoutScreeningResponse();
        if (empty($responseObject->processingStatus)) {
            $result->processingStatus = self::$REQUEST_ERROR;
        } else {
            if ($responseObject->processingStatus == self::$SCREENING_OK) {
                $result->merchantCustRef = $responseObject->merchantCustRef;
                $result->processingStatus = $responseObject->processingStatus;
                $result->replyMsgDateTime = $responseObject->replyMsgDateTime;
                $result->replyMsgId = $responseObject->replyMsgId;
                $result->requestMsgDateTime = $responseObject->requestMsgDateTime;
                $result->requestMsgId = $responseObject->requestMsgId;
                $result->transactionId = $responseObject->transactionId;
                if (!empty($responseObject->screeningDetails) && !empty($responseObject->screeningDetails->allowedCembraPayPaymentMethods)) {
                    $result->screeningDetails->allowedCembraPayPaymentMethods = $responseObject->screeningDetails->allowedCembraPayPaymentMethods;
                }
            } else {
                $result->processingStatus = $responseObject->processingStatus;
            }
        }
        return $result;
    }

    static function checkoutResponse($response)
    {
        $responseObject = json_decode($response);
        $result = new CembraPayCheckoutChkResponse();
        if (empty($responseObject->processingStatus)) {
            $result->processingStatus = self::$REQUEST_ERROR;
        } else {
            $result->processingStatus = $responseObject->processingStatus;
            if ($responseObject->processingStatus == self::$CHK_OK) {
                $result->transactionId = $responseObject->transactionId;
                $result->redirectUrlCheckout = $responseObject->redirectUrlCheckout;
            }
        }
        return $result;
    }

    static function authorizationResponse($response)
    {
        $responseObject = json_decode($response);
        $result = new CembraPayCheckoutAuthorizationResponse();
        if (empty($responseObject->processingStatus)) {
            $result->processingStatus = self::$REQUEST_ERROR;
        } else {
            $result->processingStatus = $responseObject->processingStatus;
            if ($responseObject->processingStatus == self::$AUTH_OK) {
                $result->transactionId = $responseObject->transactionId;
            }
        }
        return $result;
    }

    static function confirmTransactionResponse($response)
    {
        $responseObject = json_decode($response);
        $result = new CembraPayConfirmResponse();
        if (empty($responseObject->transactionStatus->transactionStatus)) {
            $result->transactionStatus->transactionStatus= self::$REQUEST_ERROR;
        } else {
            $result->requestMerchantId = $responseObject->requestMerchantId;
            $result->requestMsgId = $responseObject->requestMsgId;
            $result->requestMsgDateTime = $responseObject->requestMsgDateTime;
            $result->replyMsgId = $responseObject->replyMsgId;
            $result->replyMsgDateTime = $responseObject->replyMsgDateTime;
            $result->isTokenDeleted = !empty($responseObject->isTokenDeleted) ? $responseObject->isTokenDeleted : false;
            $result->transactionStatus->transactionStatus = $responseObject->transactionStatus->transactionStatus;
        }
        return $result;
    }

    static function cancelResponse($response)
    {
        $responseObject = json_decode($response);
        $result = new CembraPayCheckoutCancelResponse();
        if (empty($responseObject->processingStatus)) {
            $result->processingStatus = self::$REQUEST_ERROR;
        } else {
            if ($responseObject->processingStatus == self::$CANCEL_OK) {
                $result->processingStatus = $responseObject->processingStatus;
                $result->transactionId = !empty($responseObject->transactionId) ? $responseObject->transactionId : "";
            } else {
                $result->processingStatus = $responseObject->processingStatus;
            }
        }
        return $result;
    }

    static function settleResponse($response)
    {
        $responseObject = json_decode($response);
        $result = new CembraPayCheckoutSettleResponse();
        if (empty($responseObject->processingStatus)) {
            $result->processingStatus = self::$REQUEST_ERROR;
        } else {
            if (!empty($responseObject->processingStatus) && in_array($responseObject->processingStatus, self::$SETTLE_STATUSES)) {
                $result->processingStatus = $responseObject->processingStatus;
                $result->transactionId = !empty($responseObject->transactionId) ? $responseObject->transactionId : "";
                $result->settlementId = $responseObject->settlement->settlementId;

            } else {
                $result->processingStatus = $responseObject->processingStatus;
            }
        }
        return $result;
    }

    static function creditResponse($response)
    {
        $responseObject = json_decode($response);
        $result = new CembraPayCheckoutCreditResponse();
        if (empty($responseObject->processingStatus)) {
            $result->processingStatus = self::$REQUEST_ERROR;
        } else {
            if ($responseObject->processingStatus == self::$CREDIT_OK) {
                $result->processingStatus = $responseObject->processingStatus;
                $result->transactionId = !empty($responseObject->transactionId) ? $responseObject->transactionId : "";
            } else {
                $result->processingStatus = $responseObject->processingStatus;
            }
        }
        return $result;
    }
}