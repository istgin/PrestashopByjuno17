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
use Byjuno\ByjunoPayments\Api\CembraPayCommunicator;
use Byjuno\ByjunoPayments\Api\CembraPayConstants;
use Byjuno\ByjunoPayments\Api\CembraPayLogger;
use Byjuno\ByjunoPayments\Api\CembraPayLoginDto;

/**
 * @since 1.5.0
 */
class ByjunoCheckoutsuccessModuleFrontController extends ModuleFrontController
{
	/**
	 * @see FrontController::postProcess()
	 */
    function getAccessData($mode) {
        $accessData = new CembraPayLoginDto();
        $accessData->helperObject = $this;
        $accessData->timeout = (int)30;
        if ($mode == 'test') {
            $accessData->mode = 'test';
            $accessData->username = Configuration::get("CEMBRAPAY_TEST_CLIENT_ID");
            $accessData->password = Configuration::get("CEMBRAPAY_TEST_PASSWORD");
            $accessData->audience = "59ff4c0b-7ce8-42f0-983b-306706936fa1/.default";
            $accessToken = Configuration::get("BYJUNO_ACCESS_TOKEN_TEST");
        } else {
            $accessData->mode = 'live';
            $accessData->username = Configuration::get("CEMBRAPAY_LIVE_CLIENT_ID");
            $accessData->password = Configuration::get("CEMBRAPAY_LIVE_PASSWORD");
            $accessData->audience = "80d0ac9d-9d5c-499c-876e-71dd57e436f2/.default";
            $accessToken = Configuration::get("BYJUNO_ACCESS_TOKEN_LIVE");
        }
        $tkn = explode(CembraPayConstants::$tokenSeparator, $accessToken);
        $hash = $accessData->username.$accessData->password.$accessData->audience;
        if ($hash == $tkn[0] && !empty($tkn[1])) {
            $accessData->accessToken = $tkn[1];
        }
        return $accessData;
    }

    function saveToken($token, $accessData) {
        /* @var $accessData CembraPayLoginDto */
        $hash = $accessData->username.$accessData->password.$accessData->audience.CembraPayConstants::$tokenSeparator;
        if ($accessData->mode == 'test') {
            Configuration::updateValue("BYJUNO_ACCESS_TOKEN_TEST", $hash.$token);
        } else {
            Configuration::updateValue("BYJUNO_ACCESS_TOKEN_LIVE", $hash.$token);
        }
    }

	public function postProcess()
	{

	}
}
