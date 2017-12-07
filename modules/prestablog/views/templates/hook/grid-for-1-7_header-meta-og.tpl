{*
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
 *}

<!-- Module Presta Blog -->
{foreach from=$prestablog_fb_admins item=fbmoderator}
<meta property="fb:admins"       content="{$fbmoderator|escape:'html':'UTF-8'}" />
{/foreach}
{if $prestablog_fb_appid}
<meta property="fb:app_id"       content="{$prestablog_fb_appid|escape:'html':'UTF-8'}" />
{/if}
<meta property="og:url"          content="{$prestablog_news_meta_url|escape:'html':'UTF-8'}" />
<meta property="og:image"        content="{$prestablog_news_meta_img|escape:'html':'UTF-8'}" />
<meta property="og:title"        content="{$prestablog_news_meta->title|escape:'htmlall':'UTF-8'}" />
<meta property="og:description"  content="{$prestablog_news_meta->paragraph|escape:'htmlall':'UTF-8'}" />
<!-- Module Presta Blog -->

