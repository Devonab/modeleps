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
	$('.prestablog_mois').hide();

	if ( $('ul.prestablog_mois').hasClass('prestablog_show') )
		$('.prestablog_show').show();
	else
		$('.prestablog_annee:first').next().show();

	$('.prestablog_annee').click(function(){
		if( $(this).next().is(':hidden') ) {
			$('.prestablog_annee').next().slideUp();
			$(this).next().slideDown();
		}
		return false;
	});
});