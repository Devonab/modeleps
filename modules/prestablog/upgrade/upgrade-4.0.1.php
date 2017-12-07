<?php
/**
 * 2008 - 2017 (c) HDClic
 *
 * MODULE PrestaBlog
 *
 * @author    HDClic <prestashop@hdclic.com>
 * @copyright Copyright (c) permanent, HDClic
 * @license   Addons PrestaShop license limitation
 * @version    4.0.3
 * @link    http://www.hdclic.com
 *
 * NOTICE OF LICENSE
 *
 * Don't use this module on several shops. The license provided by PrestaShop Addons
 * for all its modules is valid only once for a single shop.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_4_0_1($module)
{
    if (!Configuration::get($module->name.'_layout_blog')) {
        Configuration::updateValue('prestablog_layout_blog', 2);
    }
    if (!Configuration::get($module->name.'_lb_title_length')) {
        Configuration::updateValue('prestablog_lb_title_length', 80);
    }
    if (!Configuration::get($module->name.'_lb_intro_length')) {
        Configuration::updateValue('prestablog_lb_intro_length', 120);
    }

    $module->deleteAdminTab();
    $module->registerAdminTab();

    Tools::clearCache();

    return true;
}
