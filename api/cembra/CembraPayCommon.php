<?php

use Byjuno\ByjunoPayments\Api\CembraPayCheckoutAutRequest;
use Byjuno\ByjunoPayments\Api\CembraPayCheckoutCancelRequest;
use Byjuno\ByjunoPayments\Api\CembraPayCheckoutChkRequest;
use Byjuno\ByjunoPayments\Api\CembraPayCheckoutCreditRequest;
use Byjuno\ByjunoPayments\Api\CembraPayCheckoutSettleRequest;
use Byjuno\ByjunoPayments\Api\CembraPayConfirmRequest;
use Byjuno\ByjunoPayments\Api\CembraPayConstants;
use Byjuno\ByjunoPayments\Api\CustomerConsents;

function Cembra_byjunoGetClientIp() {
    $ipaddress = '';
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    } else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else if(!empty($_SERVER['HTTP_X_FORWARDED'])) {
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    } else if(!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    } else if(!empty($_SERVER['HTTP_FORWARDED'])) {
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    } else if(!empty($_SERVER['REMOTE_ADDR'])) {
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    } else {
        $ipaddress = 'UNKNOWN';
    }
    return $ipaddress;
}

function Cembra_mapPaymentMethodToSpecs($method){
    $method = strtolower(str_replace(" ", "", $method));
    $IntrumMapping = array(
        'cashondelivery'	=> 'CASH-ON-DELIVERY',
        'checkmo'			=> 'INVOICE',
        'banktransfer'		=> 'PRE-PAY',
        'ccsave'			=> 'CREDIT-CARD',
        'paypal'			=> 'E-PAYMENT',
        'bankwire'			=> 'INVOICE',
        'bill'			    => 'INVOICE',
        'invoice'			=> 'INVOICE',
        'invoicepayment'	=> 'INVOICE',
        'visa'	            => 'CREDIT-CARD',
        'maestro'	        => 'CREDIT-CARD',
        'mastercard'	    => 'CREDIT-CARD',
		'bezahlenperrechnung' => 'INVOICE'
    );

    if(strpos($method, 'paypal')!==false){
        if(array_key_exists('paypal', $IntrumMapping)){
            return $IntrumMapping['paypal'];
        }
    }
    if(strpos($method, 'invoice')!==false){
        return $IntrumMapping['invoice'];
    }
    if(strpos($method, 'maestro')!==false){
        return $IntrumMapping['maestro'];
    }
    if(strpos($method, 'mastercard')!==false){
        return $IntrumMapping['mastercard'];
    }
    if(strpos($method, 'visa')!==false){
        return $IntrumMapping['visa'];
    }
    if(strpos($method, 'rechnung')!==false){
        return $IntrumMapping['bezahlenperrechnung'];
    }
    if(array_key_exists($method, $IntrumMapping)){

        return $IntrumMapping[$method];
    }
    return $method;
}

function Cembra_mapMethod($method) {
    if ($method == 'installment_3') {
        return "INSTALLMENT";
    } else if ($method == 'installment_36') {
        return "INSTALLMENT";
    } else if ($method == 'installment_12') {
        return "INSTALLMENT";
    } else if ($method == 'installment_24') {
        return "INSTALLMENT";
    } else if ($method == 'installment_4x12') {
        return "INSTALLMENT";
    } else if ($method == 'installment_4x10') {
        return "INSTALLMENT";
    } else if ($method == 'single_invoice') {
        return "INVOICE";
    } else {
        return "INVOICE";
    }
}

function Cembra_mapRepayment($type) {
    if ($type == 'installment_3') {
        return CembraPayConstants::$INSTALLMENT_3;
    } else if ($type == 'installment_4') {
        return CembraPayConstants::$INSTALLMENT_4;
    } else if ($type == 'installment_6') {
        return CembraPayConstants::$INSTALLMENT_6;
    } else if ($type == 'installment_12') {
        return CembraPayConstants::$INSTALLMENT_12;
    } else if ($type == 'installment_24') {
        return CembraPayConstants::$INSTALLMENT_24;
    } else if ($type == 'installment_36') {
        return CembraPayConstants::$INSTALLMENT_36;
    } else if ($type == 'installment_48') {
        return CembraPayConstants::$INSTALLMENT_48;
    } else if ($type == 'single_invoice') {
        return CembraPayConstants::$SINGLEINVOICE;
    } else {
        return CembraPayConstants::$CEMBRAPAYINVOICE;
    }
}

function Cembra_mapToc()
{
    $lng = Context::getContext()->language->iso_code;
    $langtoc = "EN";
    if ($lng == "en" || $lng == "de" || $lng == "fr" || $lng == "it") {
        $langtoc = strtoupper($lng);
    }
    $tocUrl = Configuration::get('BYJUNO_TOC_INSTALLMENT_' . $langtoc);
    return $tocUrl;
}

function Cembra_CreatePrestaShopRequestScreening(CartCore $cart, CustomerCore $customer, CurrencyCore $currency) {

    $b2b = Configuration::get("BYJUNO_B2B") == 'enable';
    global $cookie;

    $invoice_address = new Address($cart->id_address_invoice);
    $shipping_address = new Address($cart->id_address_delivery);
    $country = new Country($invoice_address->id_country);
    $country_shipping = new Country($shipping_address->id_country);

    $request = new CembraPayCheckoutAutRequest();
    $request->requestMsgType = CembraPayConstants::$MESSAGE_SCREENING;
    $request->requestMsgId = CembraPayCheckoutAutRequest::GUID();
    $request->requestMsgDateTime = CembraPayCheckoutAutRequest::Date();
    $request->merchantOrderRef = null;
    $request->amount = round(number_format($cart->getOrderTotal(true, Cart::BOTH), 2, '.', '') * 100);
    $request->currency = $currency->iso_code;

    $reference = "";
    if (!empty($customer->id)) {
        $reference = $customer->id;
    }
    if (empty($reference)) {
        $request->custDetails->merchantCustRef = uniqid("guest_");
        $request->custDetails->loggedIn = false;
    } else {
        $request->custDetails->merchantCustRef = (string)$reference;
        $request->custDetails->loggedIn = true;
    }
    if (!empty($invoice_address->company) && $b2b) {
        $request->custDetails->custType = CembraPayConstants::$CUSTOMER_BUSINESS;
        $request->custDetails->companyName = $invoice_address->company;
    } else {
        $request->custDetails->custType = CembraPayConstants::$CUSTOMER_PRIVATE;
    }

    $request->custDetails->firstName = html_entity_decode($invoice_address->firstname, ENT_COMPAT, 'UTF-8');
    $request->custDetails->lastName = html_entity_decode($invoice_address->lastname, ENT_COMPAT, 'UTF-8');
    $request->custDetails->language = Context::getContext()->language->iso_code;

    $request->custDetails->salutation = CembraPayConstants::$GENTER_UNKNOWN;
    if ($customer->id_gender != '0') {
        if ($customer->id_gender == '1') {
            $request->custDetails->salutation = CembraPayConstants::$GENTER_MALE;
        }
        if ($customer->id_gender == '2') {
            $request->custDetails->salutation = CembraPayConstants::$GENTER_FEMALE;
        }
    }
    if (substr($customer->birthday, 0, 4) != '0000') {
        try {
            $request->custDetails->dateOfBirth = $customer->birthday;
        } catch (Exception $e) {

        }
    }
    $request->billingAddr->addrFirstLine = html_entity_decode(trim($invoice_address->address1.' '.$invoice_address->address2), ENT_COMPAT, 'UTF-8');
    $request->billingAddr->postalCode = $invoice_address->postcode;
    $request->billingAddr->town = html_entity_decode($invoice_address->city, ENT_COMPAT, 'UTF-8');
    $request->billingAddr->country = strtoupper($country->iso_code);
    $request->custContacts->email = (string)$customer->email;
    $request->custContacts->phonePrivate = (string)$invoice_address->phone_mobile;

    $request->deliveryDetails->deliveryDetailsDifferent = true;
    $request->deliveryDetails->deliveryFirstName = html_entity_decode($shipping_address->firstname, ENT_COMPAT, 'UTF-8');
    $request->deliveryDetails->deliverySecondName =  html_entity_decode($shipping_address->lastname, ENT_COMPAT, 'UTF-8');
    if (!empty($shipping_address->company) && $b2b) {
        $request->deliveryDetails->deliveryCompanyName = html_entity_decode($shipping_address->company, ENT_COMPAT, 'UTF-8');
    }
    $request->deliveryDetails->deliverySalutation = null;

    $request->deliveryDetails->deliveryAddrFirstLine = html_entity_decode(trim($shipping_address->address1.' '.$shipping_address->address2), ENT_COMPAT, 'UTF-8');
    $request->deliveryDetails->deliveryAddrPostalCode = $shipping_address->postcode;
    $request->deliveryDetails->deliveryAddrTown = html_entity_decode($shipping_address->city, ENT_COMPAT, 'UTF-8');
    $request->deliveryDetails->deliveryAddrCountry = strtoupper($country_shipping->iso_code);


    if (Configuration::get("INTRUM_ENABLETMX") == 'true' && Configuration::get("INTRUM_TMXORGID") != '' && !empty($cookie->intrumId)) {
        $request->sessionInfo->tmxSessionId = $cookie->intrumId;
    }

    $request->sessionInfo->sessionIp = Cembra_byjunoGetClientIp();

    $customerConsents = new CustomerConsents();
    $customerConsents->consentType = "SCREENING";
    $customerConsents->consentProvidedAt = "MERCHANT";
    $customerConsents->consentDate = CembraPayCheckoutAutRequest::Date();
    $customerConsents->consentReference = "MERCHANT DATA PRIVACY";
    $request->customerConsents = array($customerConsents);

    $request->merchantDetails->transactionChannel = "WEB";
    $request->merchantDetails->integrationModule = "CembraPay Prestashop 2 module 2.0.0";

    return $request;

}

function Cembra_CreatePrestaShopRequestAut(OrderCore $order, CurrencyCore $currency, $repayment, $selected_gender = "", $selected_birthday = "", $invoiceDelivery = "") {

    $b2b = Configuration::get("BYJUNO_B2B") == 'enable';
    global $cookie;

    $customer = new Customer($order->id_customer);
    $invoice_address = new Address($order->id_address_invoice);
    $shipping_address = new Address($order->id_address_delivery);
    $country = new Country($invoice_address->id_country);
    $country_shipping = new Country($shipping_address->id_country);

    $request = new CembraPayCheckoutAutRequest();
    $request->requestMsgType = CembraPayConstants::$MESSAGE_AUTH;
    $request->requestMsgId = CembraPayCheckoutAutRequest::GUID();
    $request->requestMsgDateTime = CembraPayCheckoutAutRequest::Date();
    $request->merchantOrderRef = $order->reference;
    $request->amount = round(number_format($order->total_paid_tax_incl, 2, '.', '') * 100);
    $request->currency = $currency->iso_code;

    $reference = "";
    if (!empty($customer->id)) {
        $reference = $customer->id;
    }
    if (empty($reference)) {
        $request->custDetails->merchantCustRef = uniqid("guest_");
        $request->custDetails->loggedIn = false;
    } else {
        $request->custDetails->merchantCustRef = (string)$reference;
        $request->custDetails->loggedIn = true;
    }
    if (!empty($invoice_address->company) && $b2b) {
        $request->custDetails->custType = CembraPayConstants::$CUSTOMER_BUSINESS;
        $request->custDetails->companyName = $invoice_address->company;
    } else {
        $request->custDetails->custType = CembraPayConstants::$CUSTOMER_PRIVATE;
    }

    $request->custDetails->firstName = html_entity_decode($invoice_address->firstname, ENT_COMPAT, 'UTF-8');
    $request->custDetails->lastName = html_entity_decode($invoice_address->lastname, ENT_COMPAT, 'UTF-8');
    $request->custDetails->language = Context::getContext()->language->iso_code;

    $request->custDetails->salutation = CembraPayConstants::$GENTER_UNKNOWN;
    if ($customer->id_gender != '0') {
        if ($customer->id_gender == '1') {
            $request->custDetails->salutation = CembraPayConstants::$GENTER_MALE;
        }
        if ($customer->id_gender == '2') {
            $request->custDetails->salutation = CembraPayConstants::$GENTER_FEMALE;
        }
    }
    if ($selected_gender != "") {
        if (intval($selected_gender) == 1) {
            $request->custDetails->salutation = CembraPayConstants::$GENTER_MALE;
        }
        if (intval($selected_gender) == 2) {
            $request->custDetails->salutation = CembraPayConstants::$GENTER_FEMALE;
        }
    }

    if (substr($customer->birthday, 0, 4) != '0000') {
        try {
            $request->custDetails->dateOfBirth = $customer->birthday;
        } catch (Exception $e) {

        }
    }
    if ($selected_birthday != "") {
        $request->custDetails->dateOfBirth = $selected_birthday;
    }

    $request->billingAddr->addrFirstLine = html_entity_decode(trim($invoice_address->address1.' '.$invoice_address->address2), ENT_COMPAT, 'UTF-8');
    $request->billingAddr->postalCode = $invoice_address->postcode;
    $request->billingAddr->town = html_entity_decode($invoice_address->city, ENT_COMPAT, 'UTF-8');
    $request->billingAddr->country = strtoupper($country->iso_code);
    $request->custContacts->email = (string)$customer->email;
    $request->custContacts->phonePrivate = (string)$invoice_address->phone_mobile;

    $request->deliveryDetails->deliveryDetailsDifferent = true;
    $request->deliveryDetails->deliveryFirstName = html_entity_decode($shipping_address->firstname, ENT_COMPAT, 'UTF-8');
    $request->deliveryDetails->deliverySecondName =  html_entity_decode($shipping_address->lastname, ENT_COMPAT, 'UTF-8');
    if (!empty($shipping_address->company) && $b2b) {
        $request->deliveryDetails->deliveryCompanyName = html_entity_decode($shipping_address->company, ENT_COMPAT, 'UTF-8');
    }
    $request->deliveryDetails->deliverySalutation = null;

    $request->deliveryDetails->deliveryAddrFirstLine = html_entity_decode(trim($shipping_address->address1.' '.$shipping_address->address2), ENT_COMPAT, 'UTF-8');
    $request->deliveryDetails->deliveryAddrPostalCode = $shipping_address->postcode;
    $request->deliveryDetails->deliveryAddrTown = html_entity_decode($shipping_address->city, ENT_COMPAT, 'UTF-8');
    $request->deliveryDetails->deliveryAddrCountry = strtoupper($country_shipping->iso_code);

    if (Configuration::get("INTRUM_ENABLETMX") == 'true' && Configuration::get("INTRUM_TMXORGID") != '' && !empty($cookie->intrumId)) {
        $request->sessionInfo->tmxSessionId = $cookie->intrumId;
    }

    $request->sessionInfo->sessionIp = Cembra_byjunoGetClientIp();

    $request->cembraPayDetails->cembraPayPaymentMethod = Cembra_mapRepayment($repayment);
    if ($invoiceDelivery == 'postal') {
        $request->cembraPayDetails->invoiceDeliveryType = "POSTAL";
    } else {
        $request->cembraPayDetails->invoiceDeliveryType = "EMAIL";
    }

    $customerConsents = new CustomerConsents();
    $customerConsents->consentType = "CEMBRAPAY-TC";
    $customerConsents->consentProvidedAt = "MERCHANT";
    $customerConsents->consentDate = CembraPayCheckoutAutRequest::Date();
    $link = Cembra_mapToc();
    $exLink = explode("/", $link);
    $consentReference = end($exLink);
    if (empty($consentReference) && isset($exLink[count($exLink) - 1])) {
        $consentReference = $exLink[count($exLink) - 2];
    }
    $customerConsents->consentReference = base64_encode($consentReference);
    $request->customerConsents = array($customerConsents);

    $request->merchantDetails->transactionChannel = "WEB";
    $request->merchantDetails->integrationModule = "CembraPay Prestashop module 2.0.0";

    return $request;
}

function Cembra_CreatePrestaShopRequestChk(OrderCore $order, CurrencyCore $currency, $repayment, $successUrl, $errorUrl, $selected_gender = "", $selected_birthday = "", $invoiceDelivery = "") {

    $b2b = Configuration::get("BYJUNO_B2B") == 'enable';
    global $cookie;

    $customer = new Customer($order->id_customer);
    $invoice_address = new Address($order->id_address_invoice);
    $shipping_address = new Address($order->id_address_delivery);
    $country = new Country($invoice_address->id_country);
    $country_shipping = new Country($shipping_address->id_country);

    $request = new CembraPayCheckoutAutRequest();
    $request->requestMsgType = CembraPayConstants::$MESSAGE_CHK;
    $request->requestMsgId = CembraPayCheckoutAutRequest::GUID();
    $request->requestMsgDateTime = CembraPayCheckoutAutRequest::Date();
    $request->merchantOrderRef = $order->reference;
    $request->amount = round(number_format($order->total_paid_tax_incl, 2, '.', '') * 100);
    $request->currency = $currency->iso_code;

    $reference = "";
    if (!empty($customer->id)) {
        $reference = $customer->id;
    }
    if (empty($reference)) {
        $request->custDetails->merchantCustRef = uniqid("guest_");
        $request->custDetails->loggedIn = false;
    } else {
        $request->custDetails->merchantCustRef = (string)$reference;
        $request->custDetails->loggedIn = true;
    }
    if (!empty($invoice_address->company) && $b2b) {
        $request->custDetails->custType = CembraPayConstants::$CUSTOMER_BUSINESS;
        $request->custDetails->companyName = $invoice_address->company;
    } else {
        $request->custDetails->custType = CembraPayConstants::$CUSTOMER_PRIVATE;
    }

    $request->custDetails->firstName = html_entity_decode($invoice_address->firstname, ENT_COMPAT, 'UTF-8');
    $request->custDetails->lastName = html_entity_decode($invoice_address->lastname, ENT_COMPAT, 'UTF-8');
    $request->custDetails->language = Context::getContext()->language->iso_code;

    $request->custDetails->salutation = CembraPayConstants::$GENTER_UNKNOWN;
    if ($customer->id_gender != '0') {
        if ($customer->id_gender == '1') {
            $request->custDetails->salutation = CembraPayConstants::$GENTER_MALE;
        }
        if ($customer->id_gender == '2') {
            $request->custDetails->salutation = CembraPayConstants::$GENTER_FEMALE;
        }
    }
    if ($selected_gender != "") {
        if (intval($selected_gender) == 1) {
            $request->custDetails->salutation = CembraPayConstants::$GENTER_MALE;
        }
        if (intval($selected_gender) == 2) {
            $request->custDetails->salutation = CembraPayConstants::$GENTER_FEMALE;
        }
    }

    if (substr($customer->birthday, 0, 4) != '0000') {
        try {
            $request->custDetails->dateOfBirth = $customer->birthday;
        } catch (Exception $e) {

        }
    }
    if ($selected_birthday != "") {
        $request->custDetails->dateOfBirth = $selected_birthday;
    }

    $request->billingAddr->addrFirstLine = html_entity_decode(trim($invoice_address->address1.' '.$invoice_address->address2), ENT_COMPAT, 'UTF-8');
    $request->billingAddr->postalCode = $invoice_address->postcode;
    $request->billingAddr->town = html_entity_decode($invoice_address->city, ENT_COMPAT, 'UTF-8');
    $request->billingAddr->country = strtoupper($country->iso_code);
    $request->custContacts->email = (string)$customer->email;
    $request->custContacts->phonePrivate = (string)$invoice_address->phone_mobile;

    $request->deliveryDetails->deliveryDetailsDifferent = true;
    $request->deliveryDetails->deliveryFirstName = html_entity_decode($shipping_address->firstname, ENT_COMPAT, 'UTF-8');
    $request->deliveryDetails->deliverySecondName =  html_entity_decode($shipping_address->lastname, ENT_COMPAT, 'UTF-8');
    if (!empty($shipping_address->company) && $b2b) {
        $request->deliveryDetails->deliveryCompanyName = html_entity_decode($shipping_address->company, ENT_COMPAT, 'UTF-8');
    }
    $request->deliveryDetails->deliverySalutation = null;

    $request->deliveryDetails->deliveryAddrFirstLine = html_entity_decode(trim($shipping_address->address1.' '.$shipping_address->address2), ENT_COMPAT, 'UTF-8');
    $request->deliveryDetails->deliveryAddrPostalCode = $shipping_address->postcode;
    $request->deliveryDetails->deliveryAddrTown = html_entity_decode($shipping_address->city, ENT_COMPAT, 'UTF-8');
    $request->deliveryDetails->deliveryAddrCountry = strtoupper($country_shipping->iso_code);

    if (Configuration::get("INTRUM_ENABLETMX") == 'true' && Configuration::get("INTRUM_TMXORGID") != '' && !empty($cookie->intrumId)) {
        $request->sessionInfo->tmxSessionId = $cookie->intrumId;
    }

    $request->sessionInfo->sessionIp = Cembra_byjunoGetClientIp();

    $request->cembraPayDetails->cembraPayPaymentMethod = Cembra_mapRepayment($repayment);
    if ($invoiceDelivery == 'postal') {
        $request->cembraPayDetails->invoiceDeliveryType = "POSTAL";
    } else {
        $request->cembraPayDetails->invoiceDeliveryType = "EMAIL";
    }

    $request->merchantDetails->returnUrlSuccess = base64_encode($successUrl);
    $request->merchantDetails->returnUrlCancel = base64_encode($errorUrl);
    $request->merchantDetails->returnUrlError = base64_encode($errorUrl);

    $request->merchantDetails->transactionChannel = "WEB";
    $request->merchantDetails->integrationModule = "CembraPay Prestashop module 2.0.0";

    return $request;
}

function Byjuno_createShopRequestConfirmTransaction($transactionId)
{
    $request = new CembraPayConfirmRequest();
    $request->requestMsgId = CembraPayCheckoutChkRequest::GUID();
    $request->requestMsgDateTime = CembraPayCheckoutChkRequest::Date();
    $request->transactionId = $transactionId;
    return $request;
}

function Byjuno_CreateShopRequestBCDPCancel($amount, $orderCurrency, $orderId, $tx)
{
    $request = new CembraPayCheckoutCancelRequest();
    $request->requestMsgType = CembraPayConstants::$MESSAGE_CAN;
    $request->requestMsgId = CembraPayCheckoutAutRequest::GUID();
    $request->requestMsgDateTime = CembraPayCheckoutAutRequest::Date();
    $request->transactionId = $tx;
    $request->merchantOrderRef = $orderId;
    $request->amount = round(number_format($amount, 2, '.', '') * 100);
    $request->currency = $orderCurrency;
    $request->isFullCancelation = true;
    return $request;
}

function Cembra_CreateShopRequestSettle($doucmentId, $amount, $orderCurrency, $orderId, $tx)
{
    $request = new CembraPayCheckoutSettleRequest();
    $request->requestMsgType = CembraPayConstants::$MESSAGE_SET;
    $request->requestMsgId = CembraPayCheckoutSettleRequest::GUID();
    $request->requestMsgDateTime = CembraPayCheckoutSettleRequest::Date();
    $request->transactionId = $tx;
    $request->merchantOrderRef = $orderId;
    $request->amount = round(number_format($amount, 2, '.', '') * 100);
    $request->currency = $orderCurrency;
    $request->settlementDetails->merchantInvoiceRef = $doucmentId;
    $request->settlementDetails->isFinal = true;
    return $request;
}

function Cembra_CreateShopRequestS5Refund($documentId, $amount, $orderCurrency, $orderId, $settlementId, $tx)
{
    $request = new CembraPayCheckoutCreditRequest();
    $request->requestMsgType = CembraPayConstants::$MESSAGE_CNL;
    $request->requestMsgId = CembraPayCheckoutCreditRequest::GUID();
    $request->requestMsgDateTime = CembraPayCheckoutCreditRequest::Date();
    $request->transactionId = $tx;
    $request->merchantOrderRef = $orderId;
    $request->amount = round(number_format($amount, 2, '.', '') * 100);
    $request->currency = $orderCurrency;
    $request->settlementDetails->merchantInvoiceRef = $documentId;
    $request->settlementDetails->settlementId = $settlementId;
    return $request;
}