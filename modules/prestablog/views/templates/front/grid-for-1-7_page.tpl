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

<!-- Module Presta Blog START PAGE -->
{extends file=$layout_blog}

{block name='head_seo'}
  <title>{$meta_title|escape:'htmlall':'UTF-8'}</title>
  <meta name="description" content="{$meta_description|escape:'htmlall':'UTF-8'}">
  <meta name="keywords" content="{block name='head_seo_keywords'}{$page.meta.keywords|escape:'htmlall':'UTF-8'}{/block}">
  {if $page.meta.robots !== 'index'}
    <meta name="robots" content="{$page.meta.robots|escape:'htmlall':'UTF-8'}">
  {/if}
  {if $page.canonical}
    <link rel="canonical" href="{$page.canonical|escape:'url':'UTF-8'}">
  {/if}
{/block}

	  {block name='content'}

		{if isset($tpl_filtre_cat) && $tpl_filtre_cat}{PrestaBlogContent return=$tpl_filtre_cat}{/if}
		{if isset($tpl_menu_cat) && $tpl_menu_cat}{PrestaBlogContent return=$tpl_menu_cat}{/if}

		{if isset($tpl_unique) && $tpl_unique}{PrestaBlogContent return=$tpl_unique}{/if}
		{if isset($tpl_comment) && $tpl_comment}{PrestaBlogContent return=$tpl_comment}{/if}
		{if isset($tpl_comment_fb) && $tpl_comment_fb}{PrestaBlogContent return=$tpl_comment_fb}{/if}

		{if isset($tpl_slide) && $tpl_slide}{PrestaBlogContent return=$tpl_slide}{/if}
		{if isset($tpl_cat) && $tpl_cat}{PrestaBlogContent return=$tpl_cat}{/if}
		{if isset($tpl_all) && $tpl_all}{PrestaBlogContent return=$tpl_all}{/if}

    {/block}



<!-- /Module Presta Blog END PAGE -->
