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

this.vtip = function() {    

    this.xOffset = -10; // x distance from mouse

    this.yOffset = 10; // y distance from mouse       

    

    $(".vtip").unbind().hover(    

        function(e) {

            this.t = this.title;

            this.title = ''; 

            this.top = (e.pageY + yOffset); this.left = (e.pageX + xOffset);

            

            $('body').append( '<p id="vtip">' + this.t + '</p>' );

                        

            $('p#vtip').css("top", this.top+"px").css("left", this.left+"px").fadeIn("slow");

            

        },

        function() {

            this.title = this.t;

            $("p#vtip").fadeOut("slow").remove();

        }

    ).mousemove(

        function(e) {

            this.top = (e.pageY + yOffset);

            this.left = (e.pageX + xOffset);

                         

            $("p#vtip").css("top", this.top+"px").css("left", this.left+"px");

        }

    );            

    

};



jQuery(document).ready(function($){vtip();}) 