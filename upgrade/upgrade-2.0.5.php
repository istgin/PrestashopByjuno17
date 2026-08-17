<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_0_5($module)
{
    if ($module->isRegisteredInHook('header')) {
        $module->unregisterHook('header');
    }
    if (!$module->isRegisteredInHook('displayHeader')) {
        $module->registerHook('displayHeader');
    }
    return true;
}
