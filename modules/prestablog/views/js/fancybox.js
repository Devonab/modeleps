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

$(document).ready(function(){
   $('#prestablogfont a img').addClass('anti-fancybox');
   $('#prestablogfont img').not(".anti-fancybox").each(function() {
       $(this).wrap('<a class="fancybox" href="'+$(this).attr('src')+'" data-fancybox-group="prestablog-news-'+$('#prestablog_article').data('referenceid')+'"></a>');
       $(this).addClass('img-responsive');
   });
   $('#prestablogfont a.fancybox').fancybox();
});
