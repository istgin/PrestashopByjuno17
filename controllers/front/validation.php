<?php
/*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

use Byjuno\ByjunoPayments\Api\CembraPayAzure;
use Byjuno\ByjunoPayments\Api\CembraPayCheckoutAuthorizationResponse;
use Byjuno\ByjunoPayments\Api\CembraPayCheckoutChkResponse;
use Byjuno\ByjunoPayments\Api\CembraPayCommunicator;
use Byjuno\ByjunoPayments\Api\CembraPayConstants;
use Byjuno\ByjunoPayments\Api\CembraPayLogger;
use Byjuno\ByjunoPayments\Api\CembraPayLoginDto;

/**
 * @since 1.5.0
 */
class ByjunoValidationModuleFrontController extends ModuleFrontController
{
	public function postProcess()
	{
		global $cookie;
		$repayment = Cembra_mapRepayment(Tools::getValue('selected_plan'));
		$toc = Tools::getValue('terms_conditions');
        $invBack = Tools::getValue('cembra_selected_invoice');
        $instBack = Tools::getValue('cembra_selected_installment');
		if (empty($toc) || !$toc || $toc != "terms_conditions")
		{
		    $selected = "";
            if (!empty($invBack)) {
                $selected = "&select_payment_option=".$invBack;
            } else if (!empty($instBack)) {
                $selected = "&select_payment_option=".$instBack;
            }



			if ($repayment == 3 || $repayment == 4) {
				$backLink = "index.php?controller=order&step=1&agree_byjuno=true".$selected;
			} else {
				$backLink = "index.php?controller=order&step=1&agree_byjuno=true".$selected;
			}

			$cookie->byjuno_invoice_send = Tools::getValue('invoice_send');
            if (!empty($invBack)) {
                $cookie->byjuno_selected_plan_invoice = Tools::getValue('selected_plan');
            } else if (!empty($instBack)) {
                $cookie->byjuno_selected_plan_installment = Tools::getValue('selected_plan');
            }
			$cookie->byjuno_selected_gender = Tools::getValue('selected_gender');
			$cookie->byjuno_years = Tools::getValue('years');
			$cookie->byjuno_months = Tools::getValue('months');
			$cookie->byjuno_days = Tools::getValue('days');

			Tools::redirect($backLink);
			exit();
		}
		$isInvoice = true;
        if (!empty($instBack)) {
            $isInvoice = false;
        }

		$cookie->byjuno_invoice_send = "";
		$cookie->byjuno_selected_plan_invoice = "";
        $cookie->byjuno_selected_plan_installment = "";
		$cookie->byjuno_selected_gender = "";
		$cookie->byjuno_years = "";
		$cookie->byjuno_months = "";
		$cookie->byjuno_days = "";

		$errorlnk = $this->context->link->getModuleLink('byjuno', 'errorpayment');
		$cart = $this->context->cart;
		if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active)
			Tools::redirect('index.php?controller=order&step=1');

		// Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
		$authorized = false;
		foreach (Module::getPaymentModules() as $module)
			if ($module['name'] == 'byjuno')
			{
				$authorized = true;
				break;
			}
		if (!$authorized)
			die($this->module->l('This payment method is not available.'));

		$customer = new Customer($cart->id_customer);
		if (!Validate::isLoadedObject($customer)) {
			Tools::redirect($errorlnk);
			exit();
		}

		$invoiceDelivery = 'email';
		if (Configuration::get('BYJUNO_ALLOW_POSTAL') == 'true') {
			$invoiceDelivery = Tools::getValue('invoice_send');
			if ($invoiceDelivery != 'postal' && $invoiceDelivery != 'email') {
				$invoiceDelivery = 'email';
			}
		}
		$selected_gender = "";
		$selected_birthday = "";
		if (Configuration::get('BYJUNO_GENDER_BIRTHDAY_GENDER') == 'true') {
			$selected_gender = Tools::getValue('selected_gender');
		}
        if (Configuration::get('BYJUNO_GENDER_BIRTHDAY') == 'true') {
            $selected_birthday = Tools::getValue('years').'-'.sprintf("%02d", (int)Tools::getValue('months')).'-'.sprintf("%02d", (int)Tools::getValue('days'));
        }

		$currency = $this->context->currency;
		$total = (float)$cart->getOrderTotal(true, Cart::BOTH);
		$mailVars = null;

        if (!defined('_PS_MODULE_INTRUMCOM_API')) {
            require(_PS_MODULE_DIR_ . 'byjuno/api/cembrapay.php');
        }
        $paymentName = $this->module->getNameInvoice();
        if (!$isInvoice) {
            $paymentName = $this->module->getNameInstallment();
        }
        $ssl = Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE');
        $this->module->validateOrder($cart->id, Configuration::get('CEMBRA_ORDER_STATE_DEFAULT'), $total, $paymentName, NULL, $mailVars, (int)$currency->id, false, $customer->secure_key);
        $order = new OrderCore((int)$this->module->currentOrder);
        if (Configuration::get('CEMBRAPAY_PAYMENT_MODE') == 'checkout') {
            $successUrl = $this->context->link->getModuleLink('byjuno', 'checkoutsuccess', [], $ssl);
            $errorUrl = $this->context->link->getModuleLink('byjuno', 'checkouterror', [], $ssl);
            $requestChk = Cembra_CreatePrestaShopRequestChk($order, $this->context->currency, Tools::getValue('selected_plan'),
                $successUrl,
                $errorUrl,
                $selected_gender, $selected_birthday, $invoiceDelivery);
            $statusLog = "Checkout request";
            if ($requestChk->custDetails->custType == CembraPayConstants::$CUSTOMER_BUSINESS) {
                $statusLog = "Checkout request company";
            }
            $json = $requestChk->createRequest();
            $mode = Configuration::get("INTRUM_MODE");
            $cembrapayCommunicator = new CembraPayCommunicator(new CembraPayAzure());
            if (isset($mode) && strtolower($mode) == 'live') {
                $cembrapayCommunicator->setServer('live');
            } else {
                $cembrapayCommunicator->setServer('test');
            }
            $accessData = $this->module->getAccessData($mode);
            $response = $cembrapayCommunicator->sendCheckoutRequest($json, $accessData, function ($object, $token, $accessData) {
                $object->saveToken($token, $accessData);
            });
            $status = "";
            $responseRes = null;
            $cembraPayLogger = CembraPayLogger::getInstance();
            if (isset($response)) {
                /* @var $responseRes CembraPayCheckoutChkResponse */
                $responseRes = CembraPayConstants::checkoutResponse($response);
                $status = $responseRes->processingStatus;
                $cembraPayLogger->saveCembraLog($json, $response, $responseRes->processingStatus, $statusLog,
                    $requestChk->custDetails->firstName, $requestChk->custDetails->lastName, $requestChk->requestMsgId,
                    $requestChk->billingAddr->postalCode, $requestChk->billingAddr->town, $requestChk->billingAddr->country,
                    $requestChk->billingAddr->addrFirstLine, $responseRes->transactionId, $order->reference);
            } else {
                $cembraPayLogger->saveCembraLog($json, $response, "Query error", $statusLog,
                    $requestChk->custDetails->firstName, $requestChk->custDetails->lastName, $requestChk->requestMsgId,
                    $requestChk->billingAddr->postalCode, $requestChk->billingAddr->town, $requestChk->billingAddr->country,
                    $requestChk->billingAddr->addrFirstLine, "-", "-");
            }
            $this->context->cookie->cembra_old_cart_id = $cart->id;
            $this->context->cookie->cembra_checkout_order_id = $order->id;
            if ($status == CembraPayConstants::$CHK_OK) {
                $this->context->cookie->chk_transaction_id = $responseRes->transactionId;
                $this->context->cookie->chk_final_redirect = 'index.php?controller=order-confirmation&id_cart=' . $cart->id . '&id_module=' . $this->module->id . '&id_order=' . $this->module->currentOrder . '&key=' . $customer->secure_key;
                Tools::redirect($responseRes->redirectUrlCheckout);
            } else {
                $order->setCurrentState(Configuration::get('PS_OS_CANCELED'));
                Tools::redirect($errorlnk);
            }
        } else {
            $tocUrl = $this->module->getTocUrl();
            $requestAUT = Cembra_CreatePrestaShopRequestAut($order, $this->context->currency, Tools::getValue('selected_plan'), $selected_gender, $selected_birthday, $invoiceDelivery, $tocUrl);
            $statusLog = "Authorization request";
            if ($requestAUT->custDetails->custType == CembraPayConstants::$CUSTOMER_BUSINESS) {
                $statusLog = "Authorization request company";
            }
            $json = $requestAUT->createRequest();
            $mode = Configuration::get("INTRUM_MODE");
            $cembrapayCommunicator = new CembraPayCommunicator(new CembraPayAzure());
            if (isset($mode) && strtolower($mode) == 'live') {
                $cembrapayCommunicator->setServer('live');
            } else {
                $cembrapayCommunicator->setServer('test');
            }
            $accessData = $this->module->getAccessData($mode);
            $response = $cembrapayCommunicator->sendAuthRequest($json, $accessData, function ($object, $token, $accessData) {
                $object->saveToken($token, $accessData);
            });
            $status = "";
            $responseRes = null;
            $cembraPayLogger = CembraPayLogger::getInstance();
            if (isset($response)) {
                /* @var $responseRes CembraPayCheckoutAuthorizationResponse */
                $responseRes = CembraPayConstants::authorizationResponse($response);
                $status = $responseRes->processingStatus;
                $cembraPayLogger->saveCembraLog($json, $response, $responseRes->processingStatus, $statusLog,
                    $requestAUT->custDetails->firstName, $requestAUT->custDetails->lastName, $requestAUT->requestMsgId,
                    $requestAUT->billingAddr->postalCode, $requestAUT->billingAddr->town, $requestAUT->billingAddr->country,
                    $requestAUT->billingAddr->addrFirstLine, $responseRes->transactionId, $order->reference);
            } else {
                $cembraPayLogger->saveCembraLog($json, $response, "Query error", $statusLog,
                    $requestAUT->custDetails->firstName, $requestAUT->custDetails->lastName, $requestAUT->requestMsgId,
                    $requestAUT->billingAddr->postalCode, $requestAUT->billingAddr->town, $requestAUT->billingAddr->country,
                    $requestAUT->billingAddr->addrFirstLine, "-", "-");
            }
            if ($status == CembraPayConstants::$AUTH_OK) {
                $orderStatusChange = new OrderCore((int)$this->module->currentOrder);
                try {
                    $arrayOfTriggerDoNotChange = unserialize(Configuration::get('BYJUNO_SUCCESS_TRIGGER_NOT_MODIFY'));
                } catch (Exception $e) {
                    $arrayOfTriggerDoNotChange = false;
                }
                if ($arrayOfTriggerDoNotChange == false || !in_array($orderStatusChange->getCurrentState(), $arrayOfTriggerDoNotChange)) {
                    try {
                        $success = Configuration::get('BYJUNO_SUCCESS_TRIGGER');
                    } catch (Exception $e) {
                        $success = -1;
                    }
                    if ($success != -1) {
                        $order->setCurrentState($success);
                    }
                }
                Tools::redirect('index.php?controller=order-confirmation&id_cart=' . $cart->id . '&id_module=' . $this->module->id . '&id_order=' . $this->module->currentOrder . '&key=' . $customer->secure_key);
            } else {
                $this->context->cookie->cembra_old_cart_id = $cart->id;
                $order->setCurrentState(Configuration::get('PS_OS_CANCELED'));
                Tools::redirect($errorlnk);
            }
        }
	}
}
