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
<div id="categoriesFiltrage">
	{PrestaBlogContent return=$FiltrageCat}
	<form action="{PrestaBlogUrl}" method="post">
		<div id="prestablog_input_filtre_cat">
			{foreach from=$prestablog_search_array_cat item=cat_filtre}
				<input type="hidden" name="prestablog_search_array_cat[]" value="{$cat_filtre|escape:'htmlall':'UTF-8'}" />
			{/foreach}
		</div>
		<input class="search_query form-control ac_input" type="text" value="{$prestablog_search_query|escape:'htmlall':'UTF-8'}" placeholder="{l s='Search again on blog' mod='prestablog'}" name="prestablog_search" autocomplete="off">
		<button class="btn btn-default button-search" type="submit">
			<span>{l s='Search again on blog' mod='prestablog'}</span>
		</button>
		<div class="clear"></div>
	</form>
</div>
<!-- Module Presta Blog -->
