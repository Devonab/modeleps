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

this.screenshotPreview = function(){    

    /* CONFIG */

        

        xOffset = 10;

        yOffset = 30;

        

        // these 2 variable determine popup's distance from the cursor

        // you might want to adjust to get the right result

        

    /* END CONFIG */

    $("a.screenshot").hover(function(e){

        this.t = this.title;

        this.title = "";    

        var c = (this.t != "") ? "<br/>" + this.t : "";

        $("body").append("<p id='screenshot'><img src='"+ this.rel +"' alt='url preview' />"+ c +"</p>");                                 

        $("#screenshot")

            .css("top",(e.pageY - xOffset) + "px")

            .css("left",(e.pageX + yOffset) + "px")

            .fadeIn("fast");                        

    },

    function(){

        this.title = this.t;    

        $("#screenshot").remove();

    });    

    $("a.screenshot").mousemove(function(e){

        $("#screenshot")

            .css("top",(e.pageY - xOffset) + "px")

            .css("left",(e.pageX + yOffset) + "px");

    });            

};





// starting the script on page load

$(document).ready(function(){

    screenshotPreview();

});