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



function Timer(nPauseTime) {

    this._pauseTime = typeof nPauseTime == "undefined" ? 1000 : nPauseTime;

    this._timer = null;

    this._isStarted = false;

}



Timer.prototype.start = function () {

    if (this.isStarted())

        this.stop();

    var oThis = this;

    this._timer = window.setTimeout(function () {

        if (typeof oThis.ontimer == "function")

            oThis.ontimer();

    }, this._pauseTime);

    this._isStarted = false;

};



Timer.prototype.stop = function () {

    if (this._timer != null)

        window.clearTimeout(this._timer);

    this._isStarted = false;

};



Timer.prototype.isStarted = function () {

    return this._isStarted;

};



Timer.prototype.getPauseTime = function () {

    return this._pauseTime;

};



Timer.prototype.setPauseTime = function (nPauseTime) {

    this._pauseTime = nPauseTime;

};