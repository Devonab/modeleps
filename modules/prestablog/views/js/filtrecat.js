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

( function($) {
	$(function() {
		var selectedCatFilter = new Object();
		$("div#categoriesFiltrage select[name=SelectCat]").change(function() {
			var keyCat = $(this).val();
			if(keyCat > 0) {
				if(!(keyCat in selectedCatFilter)) {
					selectedCatFilter[ keyCat ] = $("option:selected", this).text().trim();
					$("div#categoriesForFilter").append('<div class="filtrecat" rel="'+keyCat+'">'+$("option:selected", this).text().trim()+'<div class="deleteCat" rel="'+keyCat+'">X</div></div>');
					$("option:selected", this).attr('disabled','disabled');
					$('option:first-child', this).attr("selected", "selected");
				}
			}

			$("#prestablog_input_filtre_cat").html('');
			$("div#categoriesForFilter div.filtrecat").each(function() {
				$("#prestablog_input_filtre_cat").append('<input type="hidden" name="prestablog_search_array_cat[]" value="'+$(this).attr("rel")+'" />');
			});
		});

		$('div#categoriesFiltrage').delegate('div.deleteCat','click',function() {
			var keyCat = $(this).attr('rel');
			$("div#categoriesFiltrage select[name=SelectCat] option[value='"+keyCat+"']").removeAttr('disabled');
			$('div.filtrecat[rel="'+keyCat+'"]').remove();
			delete selectedCatFilter[keyCat];
			$('div#categoriesFiltrage select[name=SelectCat] option:first-child', this).attr("selected", "selected");

			$("#prestablog_input_filtre_cat").html('');
			$("div#categoriesForFilter div.filtrecat").each(function() {
				$("#prestablog_input_filtre_cat").append('<input type="hidden" name="prestablog_search_array_cat[]" value="'+$(this).attr("rel")+'" />');
			});
		});
	});
} ) ( jQuery );