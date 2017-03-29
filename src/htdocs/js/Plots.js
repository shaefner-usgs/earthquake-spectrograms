'use strict';


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
      _swapImage,
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
      as[i].addEventListener('click', _swapImage);
    }
  };

  /**
   * Swap fullsize image with 'new' image selected by user
   *
   * @param e {Event}
   */
  _swapImage = function (e) {
    var fullsize,
        newImgSrc,
        thumb;

    fullsize = _el.querySelector('.fullsize img');
    thumb = this.querySelector('img');
    newImgSrc = thumb.getAttribute('src').replace('/tn-', '/');

    fullsize.setAttribute('src', newImgSrc);

    _updateSelected(this.parentNode);
    _updateUrl(this.getAttribute('href'));

    e.preventDefault();
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
