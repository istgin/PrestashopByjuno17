<?php



function CreateShopRequestS4($doucmentId, $amount, $orderAmount, $orderCurrency, $orderId, $customerId, $date)
{
    $request = new ByjunoS4Request();
    $request->setClientId(Configuration::get("INTRUM_CLIENT_ID"));
    $request->setUserID(Configuration::get("INTRUM_USER_ID"));
    $request->setPassword(Configuration::get("INTRUM_PASSWORD"));
    $request->setVersion("1.00");
    try {
        $request->setRequestEmail(Configuration::get("INTRUM_TECH_EMAIL"));
    } catch (Exception $e) {

    }
    $request->setRequestId(uniqid((String)$orderId . "_"));
    $request->setOrderId($orderId);
    $request->setClientRef($customerId);
    $request->setTransactionDate($date);
    $request->setTransactionAmount(number_format($amount, 2, '.', ''));
    $request->setTransactionCurrency($orderCurrency);
    $request->setAdditional1("INVOICE");
    $request->setAdditional2($doucmentId);
    $request->setOpenBalance(number_format($orderAmount, 2, '.', ''));
    return $request;
}
function CreateShopRequestS5Refund($documentId, $amount, $orderCurrency, $orderId, $customerId, $date)
{
    $request = new ByjunoS5Request();
    $request->setClientId(Configuration::get("INTRUM_CLIENT_ID"));
    $request->setUserID(Configuration::get("INTRUM_USER_ID"));
    $request->setPassword(Configuration::get("INTRUM_PASSWORD"));
    $request->setVersion("1.00");
    try {
        $request->setRequestEmail(Configuration::get("INTRUM_TECH_EMAIL"));
    } catch (Exception $e) {

    }
    $request->setRequestId(uniqid((String)$orderId . "_"));
    $request->setOrderId($orderId);
    $request->setClientRef($customerId);
    $request->setTransactionDate($date);
    $request->setTransactionAmount(number_format($amount, 2, '.', ''));
    $request->setTransactionCurrency($orderCurrency);
    $request->setTransactionType("REFUND");
    $request->setAdditional2($documentId);
    return $request;
}