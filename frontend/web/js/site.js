// Function that adds class to nav bar so we can shrink it on scroll
jQuery(window).scroll(function() {
  if ($(document).scrollTop() > 50) {
    $('nav').addClass('shrink');
  } else {
    $('nav').removeClass('shrink');
  }
});

// javascript to the top function
jQuery(document).ready(function($){
  // browser window scroll (in pixels) after which the "back to top" link is shown
  var offset = 300,
    //browser window scroll (in pixels) after which the "back to top" link opacity is reduced
    offset_opacity = 1200,
    //duration of the top scrolling animation (in ms)
    scroll_top_duration = 700,
    //grab the "back to top" link
    $back_to_top = $('.cd-top');

  //hide or show the "back to top" link
  $(window).scroll(function(){
    ( $(this).scrollTop() > offset ) ? $back_to_top.addClass('cd-is-visible') : $back_to_top.removeClass('cd-is-visible cd-fade-out');
    if( $(this).scrollTop() > offset_opacity ) { 
      $back_to_top.addClass('cd-fade-out');
    }
  });

  //smooth scroll to top
  $back_to_top.on('click', function(event){
    event.preventDefault();
    $('body,html').animate({
      scrollTop: 0 ,
      }, scroll_top_duration
    );
  });

});

// mySettings page
jQuery(document).ready(function($){

    $("#current-username").click(function(e) {
      $('.update-username').toggle('1000');
      $("span", this).toggleClass("glyphicon glyphicon-triangle-bottom glyphicon glyphicon-triangle-top");
    });

    $("#current-email").click(function(e) {     
      $('.update-email').toggle('1000');
      $("span", this).toggleClass("glyphicon glyphicon-triangle-bottom glyphicon glyphicon-triangle-top");
    });

    $("#current-password").click(function(e) {     
      $('.update-password').toggle('1000');
      $("span", this).toggleClass("glyphicon glyphicon-triangle-bottom glyphicon glyphicon-triangle-top");
    });

    $("#current-preferences").click(function(e) {     
      $('.update-preferences').toggle('1000');
      $("span", this).toggleClass("glyphicon glyphicon-triangle-bottom glyphicon glyphicon-triangle-top");
    });
});