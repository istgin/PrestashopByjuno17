<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_0_6($module)
{
    if (!$module->isRegisteredInHook('displayProductPriceBlock')) {
        if (!$module->registerHook('displayProductPriceBlock')) {
            return false;
        }
    }
    if (Configuration::get('BYJUNO_PRODUCT_BADGE_ENABLED') === false) {
        Configuration::updateValue('BYJUNO_PRODUCT_BADGE_ENABLED', 'disable');
    }
    if (Configuration::get('BYJUNO_PRODUCT_BADGE_TEXT') === false || Configuration::get('BYJUNO_PRODUCT_BADGE_TEXT') === '') {
        Configuration::updateValue('BYJUNO_PRODUCT_BADGE_TEXT', $module->l('Buy now, pay later with CembraPay invoice or installments'));
    }
    if (Configuration::get('BYJUNO_PRODUCT_BADGE_SHOW_LOGO') === false) {
        Configuration::updateValue('BYJUNO_PRODUCT_BADGE_SHOW_LOGO', 'enable');
    }
    return true;
}
