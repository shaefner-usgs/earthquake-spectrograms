'use strict';


var Plots = function (options) {
  var _initialize,
      _this,

      _addEvents,
      _swapImage,
      _updateUrl;

  _this = {};


  _initialize = function (options) {
    var lis;

    options = options || {};
    lis = options.el.querySelector('.thumbs ul');

    _addEvents(lis);
  };

  _addEvents = function (lis) {
    var i;

    for (i = 0; i < lis.length; i ++) {
      lis[i].addEventListener('click', function() {
        _swapImage();
        _updateUrl();
      });
    }
  };

  _swapImage = function () {

  };

  _updateUrl = function () {

  };


  _initialize(options);
  options = null;
  return _this;
};

module.exports = Plots;
