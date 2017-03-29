'use strict';


var Xhr = require('util/Xhr');


/**
 * Class for swapping between 2hr plots on a given day
 *
 * @param options {Object}
 *     {
 *       el: {Element}
 *     }
 */
var Plots = function (options) {
  var _initialize,
      _this,

      _el,

      _addListeners,
      _initSwap,
      _loadImage,
      _updateSelected,
      _updateUrl;

  _this = {};


  _initialize = function (options) {
    var as;

    options = options || {};
    _el = options.el;
    as = _el.querySelectorAll('.thumbs a');

    _addListeners(as);
  };

  /**
   * Add click handlers to thumbnail images
   *
   * @param as {NodeList}
   */
  _addListeners = function (as) {
    var i,
        length;

    length = as.length;
    for (i = 0; i < length; i ++) {
      as[i].addEventListener('click', _initSwap);
    }
  };

  /**
   * Show loading spinner and call methods to load image and update interface
   *
   * @param e {Event}
   */
  _initSwap = function (e) {
    var fullsize,
        newImgSrc,
        thumb;

    fullsize = _el.querySelector('.fullsize img');
    
    if (fullsize) {
      fullsize.setAttribute('src', '../../../img/spinner.gif');
      fullsize.classList.add('spinner');

      thumb = this.querySelector('img');
      newImgSrc = thumb.getAttribute('src').replace('/tn-', '/');

      _loadImage(newImgSrc, fullsize);
      _updateSelected(this.parentNode);
      _updateUrl(this.getAttribute('href'));

      e.preventDefault();
    }
  };

  /**
   * Load img via ajax and swap it when done
   *
   * @param src {String} img src attr
   * @param el {Element} fullsize img
   */
  _loadImage = function (src, el) {
    Xhr.ajax({
      url: src,
      success: function () {
        el.classList.remove('spinner');
        el.setAttribute('src', src);
      },
      error: function (status) {
        console.log(status);
      }
    });
  };

  /**
   * Update selected image in thumbnail list
   *
   * @param li {Element}
   */
  _updateSelected = function (li) {
    var i,
        length,
        lis;

    lis = _el.querySelectorAll('.thumbs li');
    length = lis.length;

    for (i = 0; i < length; i ++) {
      lis[i].classList.remove('selected');
    }

    li.classList.add('selected');
  };

  /**
   * Update browser's address bar
   *
   * @param href {String}
   */
  _updateUrl = function (href) {
    history.replaceState({}, '', href);
  };


  _initialize(options);
  options = null;
  return _this;
};

module.exports = Plots;
