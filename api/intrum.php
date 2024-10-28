<?php
/**
 * Created by PhpStorm.
 * User: i.sutugins
 * Date: 14.2.9
 * Time: 10:28
 */
define("_PS_MODULE_INTRUMCOM_API", "defined");
require(dirname(__FILE__).'/classes/ByjunoLogger.php');
require(dirname(__FILE__).'/classes/ByjunoRequest.php');
require(dirname(__FILE__).'/classes/ByjunoResponse.php');
require(dirname(__FILE__).'/classes/ByjunoCommunicator.php');
require(dirname(__FILE__).'/classes/ByjunoS5Request.php');
require(dirname(__FILE__).'/classes/ByjunoS4Request.php');
require(dirname(__FILE__).'/classes/ByjunoS4Response.php');

require(dirname(__FILE__).'/cembra/CembraPayAzure.php');
require(dirname(__FILE__).'/cembra/CembraPayCheckoutAuthorizationResponse.php');
require(dirname(__FILE__).'/cembra/CembraPayCheckoutAutRequest.php');
require(dirname(__FILE__).'/cembra/CembraPayCheckoutCancelRequest.php');
require(dirname(__FILE__).'/cembra/CembraPayCheckoutCancelResponse.php');
require(dirname(__FILE__).'/cembra/CembraPayCheckoutChkRequest.php');
require(dirname(__FILE__).'/cembra/CembraPayCheckoutChkResponse.php');
require(dirname(__FILE__).'/cembra/CembraPayCheckoutCreditRequest.php');
require(dirname(__FILE__).'/cembra/CembraPayCheckoutCreditResponse.php');
require(dirname(__FILE__).'/cembra/CembraPayCheckoutScreeningResponse.php');
require(dirname(__FILE__).'/cembra/CembraPayCheckoutSettleRequest.php');
require(dirname(__FILE__).'/cembra/CembraPayCheckoutSettleResponse.php');
require(dirname(__FILE__).'/cembra/CembraPayCommunicator.php');
require(dirname(__FILE__).'/cembra/CembraPayConfirmRequest.php');
require(dirname(__FILE__).'/cembra/CembraPayConfirmResponse.php');
require(dirname(__FILE__).'/cembra/CembraPayConstants.php');
require(dirname(__FILE__).'/cembra/CembraPayGetStatusRequest.php');
require(dirname(__FILE__).'/cembra/CembraPayGetStatusResponse.php');
require(dirname(__FILE__).'/cembra/CembraPayLogger.php');
require(dirname(__FILE__).'/cembra/CembraPayLoginDto.php');