<?php

class ByjunoCheckouterrorModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public $display_column_left = false;
    public $display_column_right = false;

    public function initContent()
	{
		global $cookie;
        if (!empty($this->context->cookie->cembra_checkout_order_id)) {
            $order = new OrderCore((int)$this->context->cookie->cembra_checkout_order_id);
            $order->setCurrentState(Configuration::get('PS_OS_CANCELED'));
        }
        $errorLink = $this->context->link->getModuleLink('byjuno', 'errorpayment');
        Tools::redirect($errorLink);
	}
}
