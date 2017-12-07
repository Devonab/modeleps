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

include_once(_PS_MODULE_DIR_.'prestablog/prestablog.php');
include_once(_PS_MODULE_DIR_.'prestablog/class/news.class.php');
include_once(_PS_MODULE_DIR_.'prestablog/class/categories.class.php');

class PrestaBlogSitemapModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    // public $display_column_left = true;
    // public $display_column_right = true;
    public $display_header = false;
    public $display_footer = false;

    public function init()
    {
        parent::init();
        if (!Configuration::get('prestablog_sitemap_actif')) {
            Tools::redirect('index.php?controller=404');
        }

        if (Tools::getValue('token') != Configuration::get('prestablog_sitemap_token')) {
            Tools::redirect('index.php?controller=404');
        }
    }

    public function display()
    {
        $module = new PrestaBlog();

        $module->createTheShopSitemap();

        return true;
    }
}
