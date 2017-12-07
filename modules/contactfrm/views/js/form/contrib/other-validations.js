/**

* 2014 Aretmic

*

* NOTICE OF LICENSE 

* 

* ARETMIC the Company grants to each customer who buys a virtual product license to use, and non-exclusive and worldwide. This license is valid 

* only once for a single e-commerce store. No assignment of rights is hereby granted by the Company to the Customer. It is also forbidden for the * Customer to resell or use on other virtual shops Products made by ARETMIC. This restriction includes all resources provided with the virtual

* product. 

*

* @author    Aretmic SA

* @copyright 2016 Aretmic SA

* @license   ARETMIC

* International Registered Trademark & Property of Aretmic SA

*/

(function($){

    if($.validationEngineLanguage == undefined || $.validationEngineLanguage.allRules == undefined )

        alert("Please include other-validations.js AFTER the translation file");

    else {

        $.validationEngineLanguage.allRules["postcodeUK"] = {

                // UK zip codes

                "regex": /^([A-PR-UWYZ0-9][A-HK-Y0-9][AEHMNPRTVXY0-9]?[ABEHMNPRVWXY0-9]? {1,2}[0-9][ABD-HJLN-UW-Z]{2}|GIR 0AA)$/,

                "alertText": "* Invalid postcode"

        };

        $.validationEngineLanguage.allRules["onlyLetNumSpec"] = {

                // Good for database fields

                "regex": /^[0-9a-zA-Z_-]+$/,

                "alertText": "* Only Letters, Numbers, hyphen(-) and underscore(_) allowed"

        };

        # more validations may be added after this point

    }

})(jQuery);

