(function($){
  function reloadErrorImage(){
    $('img:not(.processed)').addClass('processed').one('error', function(e) { $(this).attr("src",$(this).attr("src")+"?timestamp=" + new Date().getTime()) });
  }
  $(document).ready(function() {
    reloadErrorImage();
  });
  $(document).ajaxComplete(function(){
    reloadErrorImage();
  });
})(jQuery)
