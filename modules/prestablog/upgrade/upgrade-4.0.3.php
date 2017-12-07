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

function upgrade_module_4_0_3()
{
    /* installation de la liaison lookbook dans news */
    if (!Configuration::get('prestablog_nb_car_min_linklb')) {
        Configuration::updateValue('prestablog_nb_car_min_linklb', 2);
    }
    if (!Configuration::get('prestablog_nb_list_linklb')) {
        Configuration::updateValue('prestablog_nb_list_linklb', 5);
    }

    if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
        CREATE TABLE IF NOT EXISTS `'.bqSQL(_DB_PREFIX_).'prestablog_news_lookbook` (
        `id_prestablog_news_lookbook` int(10) unsigned NOT null auto_increment,
        `id_prestablog_news` int(10) unsigned NOT null,
        `id_prestablog_lookbook` int(10) unsigned NOT null,
        PRIMARY KEY (`id_prestablog_news_lookbook`))
        ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8')) {
        return false;
    }

    Tools::clearCache();

    return true;
}
