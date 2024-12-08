<?php

use Byjuno\ByjunoPayments\Api\CembraPayAzure;
use Byjuno\ByjunoPayments\Api\CembraPayCommunicator;
use Byjuno\ByjunoPayments\Api\CembraPayConstants;
use Byjuno\ByjunoPayments\Api\CembraPayLogger;

class ByjunoCheckoutsuccessModuleFrontController extends ModuleFrontController
{
    public function initContent()
	{
        if (!empty($this->context->cookie->cembra_checkout_order_id)) {
            $order = new OrderCore((int)$this->context->cookie->cembra_checkout_order_id);
            $fields = $order->getFields();
            if (!empty($fields["chk_transaction_id"])) {
                $request = Byjuno_createShopRequestConfirmTransaction(
                    $fields["chk_transaction_id"]);
                $CembraPayRequestName = "CNF";
                $json = $request->createRequest();
                $cembrapayCommunicator = new CembraPayCommunicator(new CembraPayAzure());
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
                $cembraPayLogger = CembraPayLogger::getInstance();
                if ($response) {
                    $responseRes = CembraPayConstants::confirmTransactionResponse($response);
                    if (!empty($responseRes->transactionStatus)) {
                        $transactionStatus = $responseRes->transactionStatus->transactionStatus;
                    }
                    $cembraPayLogger->saveCembraLog($json, $response, $transactionStatus, $CembraPayRequestName,
                        "-", "-", $request->requestMsgId, "-", "-", "-", "-", $fields["chk_transaction_id"], $order->reference);
                } else {
                    $cembraPayLogger->saveCembraLog($json, $response, "Query error", $CembraPayRequestName,
                        "-", "-", $request->requestMsgId, "-", "-", "-", "-", $fields["chk_transaction_id"], "-");
                }
                if (!empty($transactionStatus) && in_array($transactionStatus, CembraPayConstants::$CNF_OK_TRANSACTION_STATUSES)) {
                    $cart = $this->context->cart;
                    $customer = new Customer($cart->id_customer);
                    Tools::redirect('index.php?controller=order-confirmation&id_cart=' . $cart->id . '&id_module=' . $this->module->id . '&id_order=' . $order->id . '&key=' . $customer->secure_key);
                } else {
                    exit('bbb1');
                    $this->errorRedirect();
                }
            } else {
                exit('bbb2');
                $this->errorRedirect();
            }
        } else {
            exit('bbb3');
            $this->errorRedirect();
        }
	}

    private function errorRedirect()
    {
        $errorLink = $this->context->link->getModuleLink('byjuno', 'errorpayment');
        Tools::redirect($errorLink);
    }
}
