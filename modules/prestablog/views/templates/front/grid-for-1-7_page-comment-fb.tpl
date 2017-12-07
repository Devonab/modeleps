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
<span id="comment"></span>
<div id="prestablog-fb-comments"
		data-commentsiso="{$fb_comments_iso|escape:'html':'UTF-8'}"
		data-commentsapi="{if $fb_comments_apiId}&appId={$fb_comments_apiId|escape:'html':'UTF-8'}{/if}" >
	<div id="fb-root"></div>
	<div class="fb-comments"
		data-href="{$fb_comments_url|escape:'html':'UTF-8'}"
		data-numposts="{$fb_comments_nombre|intval}"
		data-width="100%"
		data-mobile="false"></div>
</div>
<!-- /Module Presta Blog -->
