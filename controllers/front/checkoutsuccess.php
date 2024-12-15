<?php

use Byjuno\ByjunoPayments\Api\CembraPayAzure;
use Byjuno\ByjunoPayments\Api\CembraPayCommunicator;
use Byjuno\ByjunoPayments\Api\CembraPayConstants;
use Byjuno\ByjunoPayments\Api\CembraPayLogger;

class ByjunoCheckoutsuccessModuleFrontController extends ModuleFrontController
{
    public function initContent()
	{
        if (!empty($this->context->cookie->cembra_checkout_order_id) && !empty($this->context->cookie->chk_transaction_id)) {
            $order = new OrderCore((int)$this->context->cookie->cembra_checkout_order_id);
            $cembraPayLogger = CembraPayLogger::getInstance();
            $orderRef = $cembraPayLogger->getOrderFields($order->reference);
            if (!empty($orderRef["transaction_id"])) {
                $request = Byjuno_createShopRequestConfirmTransaction(
                    $orderRef["transaction_id"]);
                $CembraPayRequestName = "CNF";
                $json = $request->createRequest();
                $cembrapayCommunicator = new CembraPayCommunicator(new CembraPayAzure());
                $mode = Configuration::get("INTRUM_MODE");
                if (isset($mode) && strtolower($mode) == 'live') {
                    $cembrapayCommunicator->setServer('live');
                } else {
                    $cembrapayCommunicator->setServer('test');
                }
                $accessData = $this->module->getAccessData($mode);
                $response = $cembrapayCommunicator->sendConfirmTransactionRequest($json, $accessData, function ($object, $token, $accessData) {
                    $object->saveToken($token, $accessData);
                });
                $transactionStatus = "";
                $responseRes = null;
                if ($response) {
                    $responseRes = CembraPayConstants::confirmTransactionResponse($response);
                    if (!empty($responseRes->transactionStatus)) {
                        $transactionStatus = $responseRes->transactionStatus->transactionStatus;
                    }
                    $cembraPayLogger->saveCembraLog($json, $response, $transactionStatus, $CembraPayRequestName,
                        "-", "-", $request->requestMsgId, "-", "-", "-", "-", $orderRef["transaction_id"], $order->reference);
                } else {
                    $cembraPayLogger->saveCembraLog($json, $response, "Query error", $CembraPayRequestName,
                        "-", "-", $request->requestMsgId, "-", "-", "-", "-", $orderRef["transaction_id"], "-");
                }
                if (!empty($transactionStatus) && in_array($transactionStatus, CembraPayConstants::$CNF_OK_TRANSACTION_STATUSES)) {
                    try {
                        $arrayOfTriggerDoNotChange = unserialize(Configuration::get('BYJUNO_SUCCESS_TRIGGER_NOT_MODIFY'));
                    } catch (Exception $e) {
                        $arrayOfTriggerDoNotChange = false;
                    }
                    if ($arrayOfTriggerDoNotChange == false || !in_array($order->getCurrentState(), $arrayOfTriggerDoNotChange)) {
                        try {
                            $success = Configuration::get('BYJUNO_SUCCESS_TRIGGER');
                        } catch (Exception $e) {
                            $success = -1;
                        }
                        if ($success != -1) {
                            $order->setCurrentState($success);
                        }
                    }
                    Tools::redirect($this->context->cookie->chk_final_redirect);
                } else {
                    $this->errorRedirect();
                }
            } else {
                $this->errorRedirect();
            }
        } else {
            $this->errorRedirect();
        }
	}

    private function errorRedirect()
    {
        $errorLink = $this->context->link->getModuleLink('byjuno', 'errorpayment');
        Tools::redirect($errorLink);
    }
}
