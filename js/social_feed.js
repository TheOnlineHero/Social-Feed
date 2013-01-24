jQuery(function() {
  var current_social_feed_upload_input;
  jQuery(".image-uploader").click(function() {
    current_social_feed_upload_input = jQuery(this).prev("input");
    jQuery.colorbox({inline:true, href:"#upload_image_container", width: "940px", height: "550px"});
  });

  jQuery("#images img").live("click", function() {
    jQuery(current_social_feed_upload_input).val(jQuery(this).attr("src"));
    jQuery("#cboxClose").click();
  });

  var bar = jQuery('.bar');
  var percent = jQuery('.percent');
  jQuery(".percent").hide();
  jQuery('#social_uploadfile_form').ajaxForm({
      beforeSend: function() {
          jQuery(".percent").hide();
          var percentVal = '0%';
          bar.width(percentVal)
          percent.html(percentVal);
      },
      uploadProgress: function(event, position, total, percentComplete) {
          jQuery(".percent").show();
          var percentVal = percentComplete + '%';
          bar.width(percentVal)
          percent.html(percentVal);
      },
      complete: function(xhr) {
          jQuery(".percent").hide();
          jQuery("#social_feed_filter_image_name").val(jQuery("#social_uploadfiles").val().match("[a-z|A-Z|\.|-|_]*$")[0]);
          jQuery("#social_feed_filter_image_name").val(jQuery("#social_feed_filter_image_name").val().replace(new RegExp("\.[a-z|A-Z]*$","i"),""));
          jQuery("#social_feed_filter_image_name").keydown();
      }
  });
});

