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
        spinnerImg,
        thumb;

    fullsize = _el.querySelector('.fullsize img');

    // Add check b/c <img> doesn't exist if 'no data'; just follow link w/o js
    if (fullsize) {
      spinnerImg = document.createElement('img');
      spinnerImg.setAttribute('src', '../../../img/spinner.gif');
      spinnerImg.classList.add('spinner');

      thumb = this.querySelector('img');
      newImgSrc = thumb.getAttribute('src').replace('/tn-', '/');

      // Hide plot and add spinner
      fullsize.classList.add('loading');
      fullsize.parentNode.appendChild(spinnerImg);

      _loadImage(newImgSrc, fullsize);
      _updateSelected(this.parentNode); // <li>
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
        // Swap plot and remove spinner
        el.setAttribute('src', src);
        el.parentNode.removeChild(_el.querySelector('.spinner'));

        // Show plot (using fadeIn animation)
        el.classList.remove('loading');
        el.classList.add('fadeIn');
        
        setTimeout(function () {
          el.classList.remove('fadeIn');
        }, 750);
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
