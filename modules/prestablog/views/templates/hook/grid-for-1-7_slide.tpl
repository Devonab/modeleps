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
<div class="prestablog_slide">
   <div class="sliders_prestablog">
   {foreach from=$ListeBlogNews item=slide name=slides}
      <a href="{PrestaBlogUrl id=$slide.id_prestablog_news seo=$slide.link_rewrite titre=$slide.title}">
         <img src="{$prestablog_theme_upimg|escape:'html':'UTF-8'}slide_{$slide.id_prestablog_news|intval}.jpg?{$md5pic|escape:'htmlall':'UTF-8'}" class="visu" alt="{$slide.title|escape:'htmlall':'UTF-8'}" title="{$slide.title|escape:'htmlall':'UTF-8'}" />
      </a>
   {/foreach}
    </div>
</div>
<div class="clearfix"></div>
<!-- /Module Presta Blog -->