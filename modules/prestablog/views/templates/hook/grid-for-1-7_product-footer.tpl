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
<section class="page-product-box">
   <h3 class="page-product-heading">{l s='Related articles on blog' mod='prestablog'}</h3>
   {if $listeNewsLinked}
      <ul class="related_blog_product">
      {foreach from=$listeNewsLinked item=Item name=myLoop}
            <li>
               <a href="{$Item.url|escape:'html':'UTF-8'}">
                  {if $Item.image_presente|intval == 1}<img src="{$prestablog_theme_upimg|escape:'html':'UTF-8'}adminth_{$Item.id|intval}.jpg?{$md5pic|escape:'htmlall':'UTF-8'}" alt="{$Item.title|escape:'htmlall':'UTF-8'}" class="lastlisteimg" />{/if}
                  <strong>{$Item.title|escape:'htmlall':'UTF-8'}</strong>
               </a>
            </li>
         {if !$smarty.foreach.myLoop.last}{/if}
      {/foreach}
      </ul>
   {else}
      <p>{l s='No related articles on blog' mod='prestablog'}</p>
   {/if}
</section>
<!-- /Module Presta Blog -->
