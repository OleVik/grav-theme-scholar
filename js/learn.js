// Scrollbar Width function
function getScrollBarWidth () {
  var inner = document.createElement('p');
  inner.style.width = "100%";
  inner.style.height = "200px";

  var outer = document.createElement('div');
  outer.style.position = "absolute";
  outer.style.top = "0px";
  outer.style.left = "0px";
  outer.style.visibility = "hidden";
  outer.style.width = "200px";
  outer.style.height = "150px";
  outer.style.overflow = "hidden";
  outer.appendChild (inner);

  document.body.appendChild (outer);
  var w1 = inner.offsetWidth;
  outer.style.overflow = 'scroll';
  var w2 = inner.offsetWidth;
  if (w1 == w2) w2 = outer.clientWidth;

  document.body.removeChild (outer);

  return (w1 - w2);
};

// debouncing function from John Hann
// http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
(function($,sr){

  var debounce = function (func, threshold, execAsap) {
    var timeout;

    return function debounced () {
      var obj = this, args = arguments;
      function delayed () {
        if (!execAsap)
          func.apply(obj, args);
        timeout = null;
      };

      if (timeout)
        clearTimeout(timeout);
      else if (execAsap)
        func.apply(obj, args);

      timeout = setTimeout(delayed, threshold || 100);
    };
  }
  // smartresize
  jQuery.fn[sr] = function(fn){  return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr); };

})(jQuery,'smartresize');


jQuery(document).ready(function(){
  var sidebarStatus = searchStatus = 'open';

  var font = 'sans';

  if (jQuery(window).width() < 768) {
    jQuery('body').addClass('sidebar-hidden');
  }

  if (localStorage.getItem('font') === null) {
    localStorage.setItem('font', font);
  } else {
    font = localStorage.getItem('font');
  }

  jQuery(document.body).addClass('font-'+font);

  jQuery('#overlay').on('click', function() {
    jQuery(document.body).toggleClass('sidebar-hidden');
    sidebarStatus = (jQuery(document.body).hasClass('sidebar-hidden') ? 'closed' : 'open');

    return false;
  });

  jQuery('[data-font-toggle]').on('click', function() {

    font = localStorage.getItem('font');

    new_font = font == 'serif' ? 'sans' : 'serif';

    jQuery(document.body).removeClass('font-'+font);
    jQuery(document.body).addClass('font-'+new_font);

    localStorage.setItem('font',new_font);

    return false;
  });

  jQuery('[data-sidebar-toggle]').on('click', function(){
    jQuery(document.body).toggleClass('sidebar-hidden');
    sidebarStatus = (jQuery(document.body).hasClass('sidebar-hidden') ? 'closed' : 'open');

    return false;
  });
  jQuery('[data-clear-history-toggle]').on('click', function(){
    sessionStorage.clear();
    location.reload();
    return false;
  });
  jQuery('[data-clear-bookmark-toggle]').on('click', function(){
    localStorage.clear();
    location.reload();
    return false;
  });
  jQuery('[data-bookmark-toggle]').on('click', function(){

    var key = jQuery('body').data('url');
    if (localStorage.getItem(key) === null) {
      localStorage.setItem(key, 1);
    } else {
      localStorage.removeItem(key);
    }
    location.reload();
    return false;
  });
  jQuery('[data-search-toggle]').on('click', function(){
    if (sidebarStatus == 'closed'){
      jQuery('[data-sidebar-toggle]').trigger('click');
      jQuery(document.body).removeClass('searchbox-hidden');
      searchStatus = 'open';

      return false;
    }

    jQuery(document.body).toggleClass('searchbox-hidden');
    searchStatus = (jQuery(document.body).hasClass('searchbox-hidden') ? 'closed' : 'open');

    return false;
  });

  var ajax;
  jQuery('[data-search-input]').on('input', function(){
    var input  = jQuery(this),
        value  = input.val(),
        items  = jQuery('[data-nav-id]');

    items.removeClass('search-match');
    if (!value.length){
      $('ul.topics').removeClass('searched');
      items.css('display', 'block');
      sessionStorage.removeItem('search-value');
      return;
    }

    sessionStorage.setItem('search-value',value);

    if (ajax && ajax.abort) ajax.abort();
    ajax = jQuery.ajax({
      url: input.data('search-input') + ':' + value
    }).done(function(data){
      if (data && data.results && data.results.length){
        items.css('display', 'none');
        $('ul.topics').addClass('searched');
        data.results.forEach(function(navitem){
          jQuery('[data-nav-id="'+navitem+'"]').css('display', 'block').addClass('search-match');
          jQuery('[data-nav-id="'+navitem+'"]').parents('li').css('display', 'block');
        });
      }
    });
    jQuery('[data-search-clear]').on('click', function(){
      jQuery('[data-search-input]').val('').trigger('input');
      sessionStorage.removeItem('search-input');
    });
  });

  if (sessionStorage.getItem('search-value')) {
    jQuery(document.body).removeClass('searchbox-hidden');
    jQuery('[data-search-input]').val(sessionStorage.getItem('search-value'));
    jQuery('[data-search-input]').trigger('input');
  }

});

jQuery(window).on('load',function(){

  function adjustForScrollbar() {
    if ((parseInt(jQuery('#body-inner').height()) + 83) >= jQuery('#body').height()) {
      jQuery('.nav.nav-next').css({'margin-right': getScrollBarWidth()});
    } else {
      jQuery('.nav.nav-next').css({'margin-right': 0});
    }
  }

  // adjust sidebar for scrollbar
  adjustForScrollbar();

  jQuery(window).smartresize(function() {
    adjustForScrollbar();
  });

  // set the progress bar width after load
  var progress = jQuery('#progress'),
  new_val  = progress.data('progress-value'),
  max      = progress.data('progress-max');

  jQuery('.progress-bar').css({width: (new_val/max * 100) + '%'});

  // store this page in session
  sessionStorage.setItem(jQuery('body').data('url'),1);

  // loop through the sessionStorage and see if something should be marked as visited
  for (var url in sessionStorage) {
    if (sessionStorage.getItem(url) == 1) jQuery('[data-nav-id="' + url + '"]').addClass('visited');
  }

  // loop through the localStorage and see if something should be marked as bookmarked
  for (var url in localStorage) {
    if (localStorage.getItem(url) == 1) {
      jQuery('[data-nav-id="' + url + '"]').addClass('bookmarked');
      if (url == jQuery('body').data('url')) {
        jQuery('body').addClass('bookmarked');
      }
    }
  }

});
