jQuery(document).ready(function ($) {

    // For Ajax
    $('.btn.load-more div').on('click', function(){

      var $this = $('body.blog .btn div');
      var count = $('.events-section-listing').find('.events-content').length;
      console.log(count)
      var data = {

          'action' : 'load_post_by_ajax',
          offset : count,
          'security' : post.security

      };

      //  if have post then append it to the structure
      $.post(post.ajaxurl, data, function (response){

          var responseCount = $.trim(response)
          // console.log(responseCount)

          if (responseCount != ''){

              $('.events-section-listing').append(response);
              if(responseCount == ''){
                  $('body.blog .btn div').hide();
              }

          }
          else{

              $('body.blog .btn div').hide();

          }

      });
  });

});