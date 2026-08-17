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
            if (!defined('_PS_MODULE_INTRUMCOM_API')) {
                require(_PS_MODULE_DIR_ . 'byjuno/api/cembrapay.php');
            }
            $oldCart = new Cart($this->context->cookie->cembra_old_cart_id);
            $duplication = $oldCart->duplicate();
            if ($duplication && Validate::isLoadedObject($duplication['cart']) && !empty($duplication['success'])) {
                Cembra_copyCheckoutSessionData($oldCart->id, $duplication['cart']->id);
                $this->context->cookie->id_cart = $duplication['cart']->id;
                $context = $this->context;
                $context->cart = $duplication['cart'];
                CartRule::autoAddToCart($context);
            }
            unset($this->context->cookie->cembra_old_cart_id);
            $this->context->cookie->write();
        }
		$this->display_column_left = false;
		parent::initContent();
        $this->context->smarty->assign('cmbrlink', Context::getContext()->link);
		$this->setTemplate('module:byjuno/views/templates/front/payment_error.tpl');
	}
}
