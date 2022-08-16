$(document).ready(function($){

  // Nav Menu
  $(".mobile_menu_bar").click(function(){
      $(".hamburger").toggleClass("is-active");
  });

  $(".scroll-to-contact").click(function() {
    $('html,body').animate({
        scrollTop: $(".contact-section").offset().top},
        1300);
  });
  // As Seen On Slider
  $('.mq-co-logo-section').slick({
    slidesToShow: 7,
    slidesToScroll: 1,
    // autoplay: true,
    // autoplaySpeed: 2000,
    responsive: [
      {
        breakpoint: 1441,
        settings: {
          slidesToShow: 6
        }
      },
      {
        breakpoint: 1281,
        settings: {
          slidesToShow: 5
        }
      },
      {
        breakpoint: 981,
        settings: {
          slidesToShow: 4
        }
      },
      {
        breakpoint: 668,
        settings: {
          slidesToShow: 3
        }
      },
    ]
  });

  //  Speaker Section
  $('.mq-speaker-post-section').slick({
    slidesToShow: 3,
    slidesToScroll: 1,
    responsive: [
      {
        breakpoint: 867,
        settings: {
          slidesToShow: 1,
          centerMode: true,
        }
      },
    ]
  });
  
  // Speaker Video Function
  $('.overlay-image').click(function(event){
    $(this).css("opacity" , "0");
    $(this).css("z-index" , "-1");
    $('.speaker-content video').trigger('play');
  });
  
  
  if (window.matchMedia('(max-width: 867px)').matches) {
  
    
}
// reviews section slider
if (window.matchMedia('(max-width: 1282px)').matches) {
  $('.reviews-section-posttype').slick({
    slidesToShow: 2,
    slidesToScroll: 1,
    arrows: false,
    dots:false,
    responsive: [
      {
        breakpoint: 581 ,
          settings: {
            slidesToShow: 1
          }
      }
    ]
  })
    
}
$('.events-section').slick({
  slidesToShow: 3,
  slidesToScroll: 1,
  arrows: false,
  dots:false,
  responsive: [
    {
      breakpoint: 1167,
        settings: {
          slidesToShow: 2
        }
    },
    {
      breakpoint: 481,
        settings: {
          slidesToShow: 1,
          centerMode: true,
        }
    }
  ]

})

});