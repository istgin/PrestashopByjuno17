<?php

class ByjunoErrorpaymentModuleFrontController extends ModuleFrontController
{
	public $ssl = true;
	public $display_column_left = false;
	public $display_column_right = false;
	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
        if (!empty($this->context->cookie->cembra_old_cart_id)) {
            $oldCart = new Cart($this->context->cookie->cembra_old_cart_id);
            $duplication = $oldCart->duplicate();
            if ($duplication && Validate::isLoadedObject($duplication['cart']) && !empty($duplication['success'])) {
                $this->context->cookie->id_cart = $duplication['cart']->id;
                $context = $this->context;
                $context->cart = $duplication['cart'];
                CartRule::autoAddToCart($context);
                $this->context->cookie->write();
            }
        }
		$this->display_column_left = false;
		parent::initContent();
		$this->setTemplate('module:byjuno/views/templates/front/payment_error.tpl');
	}
}
