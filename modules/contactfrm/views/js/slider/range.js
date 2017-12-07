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

function Range() {

    this._value = 0;

    this._minimum = 0;

    this._maximum = 50500;

    this._extent = 0;



    this._isChanging = false;

}



Range.prototype.setValue = function (value) {

    value = Math.round(parseFloat(value));

    if (isNaN(value)) return;

    if (this._value != value) {

        if (value + this._extent > this._maximum)

            this._value = this._maximum - this._extent;

        else if (value < this._minimum)

            this._value = this._minimum;

        else

            this._value = value;

        if (!this._isChanging && typeof this.onchange == "function")

             this.onchange();

    }

};



Range.prototype.getValue = function () {

    return this._value;

};



Range.prototype.setExtent = function (extent) {

    if (this._extent != extent) {

        if (extent < 0)

            this._extent = 0;

        else if (this._value + extent > this._maximum)

            this._extent = this._maximum - this._value;

        else

            this._extent = extent;

        if (!this._isChanging && typeof this.onchange == "function")

            this.onchange();

    }

};



Range.prototype.getExtent = function () {

    return this._extent;

};



Range.prototype.setMinimum = function (minimum) {

    if (this._minimum != minimum) {

        var oldIsChanging = this._isChanging;

        this._isChanging = true;



        this._minimum = minimum;



        if (minimum > this._value)

            this.setValue(minimum);

        if (minimum > this._maximum) {

            this._extent = 0;

            this.setMaximum(minimum);

            this.setValue(minimum)

        }

        if (minimum + this._extent > this._maximum)

            this._extent = this._maximum - this._minimum;



        this._isChanging = oldIsChanging;

        if (!this._isChanging && typeof this.onchange == "function")

            this.onchange();

    }

};



Range.prototype.getMinimum = function () {

    return this._minimum;

};



Range.prototype.setMaximum = function (maximum) {

    if (this._maximum != maximum) {

        var oldIsChanging = this._isChanging;

        this._isChanging = true;



        this._maximum = maximum;



        if (maximum < this._value)

            this.setValue(maximum - this._extent);

        if (maximum < this._minimum) {

            this._extent = 0;

            this.setMinimum(maximum);

            this.setValue(this._maximum);

        }

        if (maximum < this._minimum + this._extent)

            this._extent = this._maximum - this._minimum;

        if (maximum < this._value + this._extent)

            this._extent = this._maximum - this._value;



        this._isChanging = oldIsChanging;

        if (!this._isChanging && typeof this.onchange == "function")

            this.onchange();

    }

};



Range.prototype.getMaximum = function () {

    return this._maximum;

};

