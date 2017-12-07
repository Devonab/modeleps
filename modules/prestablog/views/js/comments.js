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

$(document).ready(function() {
	if ( $("#submitOk").length ) {
		$('html, body').animate({scrollTop: $("#submitOk").offset().top}, 750);
	}

	if ( $("#errors").length ) {
		$('html, body').animate({scrollTop: $("#errors").offset().top}, 750);
	}

 	$('#comments').show();

	$("#with-http").hide();

	$("#url").focus(function() { $("#with-http").fadeIn(); });

	$("#url").focusout(function() { $("#with-http").fadeOut(); });
});