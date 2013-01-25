<?php
/*
Plugin Name: Social Feed
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: Display Social Feed from Facebook.
Version: 1.0
Author: Tom Skroza
Author URI: 
License: GPL2
*/
require_once("facebook-php-sdk/src/facebook.php");
function social_feed_activate() {
  add_option( "mm_facebook_id", "", "", "yes" );
  add_option( "mm_facebook_appid", "", "", "yes" );
  add_option( "mm_facebook_secret", "", "", "yes" );
  add_option( "mm_facebook_img_profile_url", "", "", "yes" );
  add_option( "mm_facebook_page", "", "", "yes" );
  add_option( "mm_facebook_chars_per_post", "", "", "yes" );
  add_option( "mm_facebook_chars_per_comment", "", "", "yes" );
  add_option( "mm_facebook_max_posts", "", "", "yes" );
  add_option( "mm_social_css", "", "", "yes" );

	update_option( "mm_social_css", "#content #social_facebook_feed li, #content #social_twitter_feed li, #content #blog_feed li {\nlist-style: none;\nlist-style-image: none;\nwidth: 260px;\nheight: 80px;\n}\n#social_facebook_feed li img, #social_twitter_feed li img, #blog_feed li img {\nfloat: left;\nmargin-right: 10px;\n}" );

}
register_activation_hook( __FILE__, 'social_feed_activate' );


function social_feed_deactivate() {
  // delete_option( "mm_facebook_id");
  // delete_option( "mm_facebook_appid");
  // delete_option( "mm_facebook_secret");
  // delete_option( "mm_facebook_img_profile_url");
  // delete_option( "mm_facebook_page");
}
register_deactivation_hook( __FILE__, 'social_feed_deactivate' );

add_action('admin_menu', 'register_social_feed_page');

function register_social_feed_page() {
   add_menu_page('Social Feed', 'Social Feed', 'manage_options', 'social_feed/social_feed.php', 'social_feed_settings_page',   plugins_url('social_feed/facebook.png'), '');
}

add_action( 'admin_init', 'register_social_search_settings' );
function register_social_search_settings() {
  $filter_image_name = $_POST["social_feed_filter_image_name"];
  if ($filter_image_name != "") {
    $images = tom_get_results("posts", "*", "post_type='attachment' AND post_title LIKE '%$filter_image_name%' AND post_mime_type IN ('image/png', 'image/jpg', 'image/jpeg', 'image/gif')", array("post_date DESC"), "7");
    echo "<ul id='images'>";
    foreach ($images as $image) { 
        ?>
        <li>
          <img style='width: 100px; min-height: 100px' src='<?php echo($image->guid); ?>' />
        </li>

    <?php }
    echo "</ul>";
    exit();
  }
} 

add_action( 'admin_init', 'register_social_upload_settings' );
function register_social_upload_settings() {
  $social_uploadfiles = $_FILES['social_uploadfiles'];

  if (is_array($social_uploadfiles)) {

    foreach ($social_uploadfiles['name'] as $key => $value) {

      // look only for uploded files
      if ($social_uploadfiles['error'][$key] == 0) {

        $filetmp = $social_uploadfiles['tmp_name'][$key];

        //clean filename and extract extension
        $filename = $social_uploadfiles['name'][$key];

        // get file info
        // @fixme: wp checks the file extension....
        $filetype = wp_check_filetype( basename( $filename ), null );
        $filetitle = preg_replace('/\.[^.]+$/', '', basename( $filename ) );
        $filename = $filetitle . '.' . $filetype['ext'];
        $upload_dir = wp_upload_dir();

        /**
         * Check if the filename already exist in the directory and rename the
         * file if necessary
         */
        $i = 0;
        while ( file_exists( $upload_dir['path'] .'/' . $filename ) ) {
          $filename = $filetitle . '_' . $i . '.' . $filetype['ext'];
          $i++;
        }
        $filedest = $upload_dir['path'] . '/' . $filename;

        /**
         * Check write permissions
         */
        if ( !is_writeable( $upload_dir['path'] ) ) {
          $this->msg_e('Unable to write to directory %s. Is this directory writable by the server?');
          return;
        }

        /**
         * Save temporary file to uploads dir
         */
        if ( !@move_uploaded_file($filetmp, $filedest) ){
          $this->msg_e("Error, the file $filetmp could not moved to : $filedest ");
          continue;
        }

        $attachment = array(
          'post_mime_type' => $filetype['type'],
          'post_title' => $filetitle,
          'post_content' => '',
          'post_status' => 'inherit',
        );

        $attach_id = wp_insert_attachment( $attachment, $filedest );
        $attach_data = wp_generate_attachment_metadata( $attach_id, $filedest );
        wp_update_attachment_metadata( $attach_id,  $attach_data );
        preg_match("/\/wp-content(.+)$/", $filedest, $matches, PREG_OFFSET_CAPTURE);
        tom_update_record_by_id("posts", array("guid" => get_option("siteurl").$matches[0][0]), "ID", $attach_id);
        echo $filedest;
      }
    }   
  }
}

//call register settings function
add_action( 'admin_init', 'register_social_feed_settings' );
function register_social_feed_settings() {
	//register our settings
	register_setting( 'social-feed-group', 'mm_facebook_id' );
	register_setting( 'social-feed-group', 'mm_facebook_appid' );
	register_setting( 'social-feed-group', 'mm_facebook_secret' );
	register_setting( 'social-feed-group', 'mm_facebook_access_token' );
	register_setting( 'social-feed-group', 'mm_facebook_img_profile_url' );
	register_setting( 'social-feed-group', 'mm_facebook_page' );
	register_setting( 'social-feed-group', 'mm_facebook_chars_per_post' );
	register_setting( 'social-feed-group', 'mm_facebook_chars_per_comment' );
	register_setting( 'social-feed-group', 'mm_facebook_max_posts' );
	register_setting( 'social-feed-group', 'mm_twitter_screen_name' );
	register_setting( 'social-feed-group', 'mm_twitter_chars_per_post' );
	register_setting( 'social-feed-group', 'mm_twitter_max_posts' );
	register_setting( 'social-feed-group', 'mm_blog_rss_feed_url' );
	register_setting( 'social-feed-group', 'mm_blog_rss_feed_chars_per_post' );
	register_setting( 'social-feed-group', 'mm_blog_rss_feed_max_posts' );
	register_setting( 'social-feed-group', 'mm_social_css' );
	register_setting( 'social-feed-group', 'mm_last_date_cached');
	register_setting( 'social-feed-group', 'mm_facebook_recent_cached');

  @check_social_feed_dependencies_are_active(
    "Social Feed", 
    array(
      "Tom M8te" => array("plugin"=>"tom-m8te/tom-m8te.php", "url" => "http://downloads.wordpress.org/plugin/tom-m8te.zip", "version" => "1.1"),
      "JQuery Colorbox" => array("plugin"=>"jquery-colorbox/jquery-colorbox.php", "url" => "http://downloads.wordpress.org/plugin/jquery-colorbox.zip"))
  );
}

function social_feed_settings_page() {
    wp_enqueue_script('jquery');
    wp_register_script( 'my-jquery-colorbox', get_option("siteurl")."/wp-content/plugins/jquery-colorbox/js/jquery.colorbox-min.js" );
    wp_enqueue_script('my-jquery-colorbox');
    wp_register_script( 'my-form-script', plugins_url('js/jquery.form.js', __FILE__) );
    wp_enqueue_script('my-form-script');
    wp_register_script( 'my-social-feed', plugins_url('js/social_feed.js', __FILE__) );
    wp_enqueue_script('my-social-feed');
    wp_register_style( 'my-jquery-colorbox-style',get_option("siteurl")."/wp-content/plugins/jquery-colorbox/themes/theme1/colorbox.css");
    wp_enqueue_style('my-jquery-colorbox-style');
?>

<style>
  #upload_image_container, #images {display: none;}
  #cboxWrapper #upload_image_container, #cboxWrapper #images {display: block;}
  ul#images li {float: left; margin-right: 5px;}
</style>

<script language="javascript">
	jQuery(function() {
	  jQuery("#social_feed_filter_image_name").live("keydown", function() {
	      if (jQuery(this).val().length < 2) {
	        jQuery("#images_container").html("");
	      } else {
	        jQuery.post("<?php echo(get_option('siteurl')); ?>/wp-admin/admin.php?page=social_feed/social_feed.php", { social_feed_filter_image_name: jQuery(this).val() },
	            function(data) {
	              jQuery("#images_container").html(data);
	            }
	        );
	      }
	  });
	});
</script>

<div id="upload_image_container">
  <div class="wrap">
<h2>Social Feed</h2>
<div class="postbox " style="display: block; ">
<div class="inside">
  <table class="form-table">
    <tbody>

      <tr valign="top">
        <th scope="row">
          <label for="filter_image_name">Upload</label>
        </th>
        <td>
          <form name="social_uploadfile" id="social_uploadfile_form" method="POST" enctype="multipart/form-data" action="#social_uploadfile" accept-charset="utf-8" >
            <input type="file" name="social_uploadfiles[]" id="social_uploadfiles" size="35" class="social_uploadfiles" />
            <input class="button-primary" type="submit" name="social_uploadfile" id="social_uploadfile_btn" value="Upload"  />
          </form>
          <div class="progress">
              <div class="bar"></div >
              <div class="percent">0%</div >
          </div>
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">
          <label for="social_feed_filter_image_name">Search</label>
        </th>
        <td>
          <input type="text" id="social_feed_filter_image_name" name="social_feed_filter_image_name" value="" />
        </td>
      </tr>
      <tr>
        <td></td>
        <td><div id="images_container"></div></td>
      </tr>
    </tbody>
  </table>
</div>
</div>
</div>
</div>


<div class="wrap">
<h2>Social Feed</h2>
<div class="postbox " style="display: block; ">
<div class="inside">
<form method="post" action="options.php">
  <?php settings_fields( 'social-feed-group' ); ?>
  <h3>Facebook</h3>
  <table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row">
					<label for="mm_facebook_id">Facebook ID</label>
				</th>
				<td>
					<input type="text" name="mm_facebook_id" value="<?php echo get_option('mm_facebook_id'); ?>" />
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for="mm_facebook_appid">Facebook App ID</label>
				</th>
				<td>
					<input type="text" name="mm_facebook_appid" value="<?php echo get_option('mm_facebook_appid'); ?>" />
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for="mm_facebook_secret">Facebook Secret Token</label>
				</th>
				<td>
					<input type="text" name="mm_facebook_secret" value="<?php echo get_option('mm_facebook_secret'); ?>" />
				</td>
			</tr>

<!-- 			<tr valign="top">
				<th scope="row">
					<label for="mm_facebook_access_token">Facebook Access Token</label>
				</th>
				<td>
					<input type="text" name="mm_facebook_access_token" value="<?php echo get_option('mm_facebook_access_token'); ?>" />
				</td>
			</tr> -->

			<tr valign="top">
				<th scope="row">
					<label for="mm_facebook_img_profile_url">Facebook Profile Image URL</label>
				</th>
				<td>
					<input type="text" name="mm_facebook_img_profile_url" value="<?php echo get_option('mm_facebook_img_profile_url'); ?>" />
					<input type="button" class="image-uploader" value="Upload" />
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for="mm_facebook_page">Facebook Page URL</label>
				</th>
				<td>
					<input type="text" name="mm_facebook_page" value="<?php echo get_option('mm_facebook_page'); ?>" />
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for="mm_facebook_chars_per_post">Max Characters Per Post</label>
				</th>
				<td>
					<input type="text" name="mm_facebook_chars_per_post" value="<?php echo get_option('mm_facebook_chars_per_post'); ?>" />
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for="mm_facebook_chars_per_comment">Max Characters Per Comment</label>
				</th>
				<td>
					<input type="text" name="mm_facebook_chars_per_comment" value="<?php echo get_option('mm_facebook_chars_per_comment'); ?>" />
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for="mm_facebook_max_posts">Max No of Posts</label>
				</th>
				<td>
					<input type="text" name="mm_facebook_max_posts" value="<?php echo get_option('mm_facebook_max_posts'); ?>" />
				</td>
			</tr>

		</tbody>
	</table>

<h3>Twitter</h3>
  <table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row">
					<label for="mm_twitter_screen_name">Screen Name</label>
				</th>
				<td>
					<input type="text" name="mm_twitter_screen_name" value="<?php echo get_option('mm_twitter_screen_name'); ?>" />
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for="mm_twitter_chars_per_post">Max Characters Per Post</label>
				</th>
				<td>
					<input type="text" name="mm_twitter_chars_per_post" value="<?php echo get_option('mm_twitter_chars_per_post'); ?>" />
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for="mm_twitter_max_posts">Max No of Posts</label>
				</th>
				<td>
					<input type="text" name="mm_twitter_max_posts" value="<?php echo get_option('mm_twitter_max_posts'); ?>" />
				</td>
			</tr>

		</tbody>
	</table>

<h3>Blog</h3>
  <table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row">
					<label for="mm_blog_rss_feed_url">Blog RSS URL</label>
				</th>
				<td>
					<input type="text" name="mm_blog_rss_feed_url" value="<?php echo get_option('mm_blog_rss_feed_url'); ?>" />
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for="mm_blog_rss_feed_chars_per_post">Max Characters Per Post</label>
				</th>
				<td>
					<input type="text" name="mm_blog_rss_feed_chars_per_post" value="<?php echo get_option('mm_blog_rss_feed_chars_per_post'); ?>" />
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for="mm_blog_rss_feed_max_posts">Max No of Posts</label>
				</th>
				<td>
					<input type="text" name="mm_blog_rss_feed_max_posts" value="<?php echo get_option('mm_blog_rss_feed_max_posts'); ?>" />
				</td>
			</tr>

		</tbody>
	</table>

	<h3>Social Feed Css</h3>
  <table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row">
					<label for="mm_social_css">Css</label>
				</th>
				<td>
<textarea name="mm_social_css" cols="200" rows="6"><?php echo get_option('mm_social_css'); ?></textarea>
				</td>
			</tr>
		</tbody>
	</table>
  		
	<p class="submit">
		<input type="submit" name="Submit" value="Update Options">
	</p>
</form>
</div>
</div>
</div>

<?php }

class SocialFeedRecentFacebookWidget extends WP_Widget {

	function SocialFeedRecentFacebookWidget() {
		// Instantiate the parent object
		parent::__construct( false, 'Social Feed Recent Facebook Post' );
	}

	function widget( $args, $instance ) {
		// Widget output
    most_recent_facebook_feed();
	}

	function update( $new_instance, $old_instance ) {
		// Save widget options
	}

	function form( $instance ) {
		// Output admin widget options form
	}
}

function social_feed_register_recent_facebook_widgets() {
	register_widget( 'SocialFeedRecentFacebookWidget' );
}

add_action( 'widgets_init', 'social_feed_register_recent_facebook_widgets' );


class SocialFeedRecentTwitterWidget extends WP_Widget {

	function SocialFeedRecentTwitterWidget() {
		// Instantiate the parent object
		parent::__construct( false, 'Social Feed Recent Twitter Post' );
	}

	function widget( $args, $instance ) {
		// Widget output
		most_recent_twitter_feed();
	}

	function update( $new_instance, $old_instance ) {
		// Save widget options
	}

	function form( $instance ) {
		// Output admin widget options form
	}
}

function social_feed_register_recent_twitter_widgets() {
	register_widget( 'SocialFeedRecentTwitterWidget' );
}

add_action( 'widgets_init', 'social_feed_register_recent_twitter_widgets' );


function blog_content() {
		// RSS Feed
		$rss_url = get_option('mm_blog_rss_feed_url');
		if ($rss_url != null) {
			$sxml = simplexml_load_file($rss_url);
			$blog_feed_content = "";

			$count = 0;
	    foreach ($sxml->channel->item as $item) {
	    	if ($count < get_option('mm_blog_rss_feed_max_posts')) {
	    		$description = htmlspecialchars(facebook_TokenTruncate($item->description, get_option('mm_blog_rss_feed_chars_per_post')));
		    	$blog_feed_content .= "<li><img src='".get_option("siteurl")."/wp-content/plugins/social_feed/blog.jpg'/>$item->title - $description <a target='_blank' href='$item->guid'>...</a></li>";
		    	$count++;
		    } else {
		    	break;
		    }
	    }
			return $blog_feed_content;
		} else {
			return "";
		}
}

function twitter_content() {
		// jSON URL which should be requested
		$json_url = "https://api.twitter.com/1/statuses/user_timeline.json?include_entities=true&include_rts=true&screen_name=".get_option('mm_twitter_screen_name')."&count=".get_option('mm_twitter_max_posts');
		// Initializing curl
		$ch = curl_init( $json_url );
		 
		// Configuring curl options
		$options = array(
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_SSL_VERIFYPEER, false,
		CURLOPT_HTTPHEADER => array('Content-type: application/json')
		);

		// Setting curl options
		curl_setopt_array( $ch, $options );
		 
		// Getting results
		$result =  curl_exec($ch); // Getting jSON result string

		$obj = json_decode($result);

		if ($result) {
			if (is_array($obj)) {
				$twitter_feed_content = "";
				$caption = "";

				if (is_array($obj)) {
					for($i=0;$i<get_option('mm_twitter_max_posts');$i++) {
						$twitter_feed_content .= "<li><img src='".$obj[$i]->{"user"}->{"profile_image_url"}."'/>".twitterReplaceURLWithHTMLLinks(facebook_TokenTruncate($obj[$i]->{'text'}, get_option('mm_twitter_chars_per_post')), "link")."</li>";
					}
				}
			}
		}

		return $twitter_feed_content.$comments;
}

function facebook_content() {
		//facebook feed url
    $url="http://www.facebook.com/feeds/page.php?id=".get_option("mm_facebook_id")."&format=atom10";
    
    //load and setup CURL
    $c = curl_init();
    
    //set options and make it up to look like firefox
		$userAgent = "Firefox (WindowsXP) - Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6";
		curl_setopt($c, CURLOPT_USERAGENT, $userAgent);
		curl_setopt($c, CURLOPT_URL,$url);
		curl_setopt($c, CURLOPT_FAILONERROR, true);
		curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($c, CURLOPT_AUTOREFERER, true);
		curl_setopt($c, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($c, CURLOPT_VERBOSE, false);     
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    
    //get data from facebook and decode XML
    $page = curl_exec($c);
    $pxml= new SimpleXMLElement($page);

    //close the connection
    curl_close($c);

    $myPosts = $pxml->entry;
    $fb_feed_content = "";
    foreach($myPosts as $dPost){
    	$fb_feed_content .= "<li><a target='_blank' href='".get_option("mm_facebook_page")."'><img src='".get_option('mm_facebook_img_profile_url')."'/></a>".facebookReplaceURLWithHTMLLinks(facebook_TokenTruncate($dPost->content, get_option('mm_facebook_chars_per_post')), $caption)." <a target='_blank' href='".get_option('mm_facebook_page')."'>...</a></li>";
    }

		return $fb_feed_content;
}

function mytheme_content_filter( $content ) {
	if (strpos($content, "<!-- Social Media -->") > 0) {
		$fb_feed_content = facebook_content();
		$twitter_feed_content = twitter_content();
		$blog_feed_content = blog_content();

		$feed_content = "<ul id='social_facebook_feed'>".$fb_feed_content."</ul><ul id='social_twitter_feed'>".$twitter_feed_content."</ul><ul id='blog_feed'>".$blog_feed_content."</ul>";

		return str_replace("<!-- Social Media -->", $feed_content, $content);		
	} else {
		return $content;
	}
}
add_filter( 'the_content', 'mytheme_content_filter' );

function most_recent_blog_feed() {
		// RSS Feed
		$rss_url = get_option('mm_blog_rss_feed_url');
		if ($rss_url != null) {
			$sxml = simplexml_load_file($rss_url);
			$blog_feed_content = "";
	    $item = $sxml->channel->item[0];
	    $description = facebookReplaceURLWithHTMLLinks(facebook_TokenTruncate($item->description, get_option('mm_blog_rss_feed_chars_per_post')), "");
		  $blog_feed_content .= "<li>$item->title - $description <a target='_blank' href='$item->guid'>...</a></li>";
			return $blog_feed_content;
		} else {
			return "";
		}
}

function most_recent_facebook_feed() {

	if (get_option("mm_last_date_cached") != date("d/m/y")) {
		$config = array();
		$config['appId'] = get_option("mm_facebook_appid");
		$config['secret'] = get_option("mm_facebook_secret");
	  $config['fileUpload'] = false; // optional

	  $facebook = new Facebook($config);

	  //echo $facebook->getAccessToken();
	  //344617158898614|6dc8ac871858b34798bc2488200e503d
		// jSON URL which should be requested

		$json_url = "https://graph.facebook.com/".get_option("mm_facebook_id")."/feed?access_token=".urldecode($facebook->getAccessToken());
		// Initializing curl
		$ch = curl_init( $json_url );
		 
		// Configuring curl options
		$userAgent = "Firefox (WindowsXP) - Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6";
		$options = array(
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_SSL_VERIFYPEER, false,
		CURLOPT_HTTPHEADER => array('Content-type: application/json'),
		CURLOPT_USERAGENT => $userAgent,
		CURLOPT_URL => $json_url,
		CURLOPT_FAILONERROR => true,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_AUTOREFERER => true,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_VERBOSE => false,    
	  CURLOPT_RETURNTRANSFER => 1
		);

		// Setting curl options
		curl_setopt_array( $ch, $options );
		 
		// Getting results
		$result =  curl_exec($ch); // Getting jSON result string

		$obj = json_decode($result);

		$fb_feed_content = "";
		$caption = "";

		$data_feed = $obj->{'data'}[0];

		if ($data_feed->{'type'} != "") {
			$message = "";

			if ($data_feed->{'type'} == "link") {
				$message = $data_feed->{'message'};
				$caption = $data_feed->{'caption'};
			}
			if ($data_feed->{'type'} == "photo" || $data_feed->{'type'} == "status") {
				$message = $data_feed->{'story'};
			}

			$fb_feed_content .= "<li><img src='".get_option('mm_facebook_img_profile_url')."'/>".facebookReplaceURLWithHTMLLinks(facebook_TokenTruncate($message, get_option('mm_facebook_chars_per_post')), $caption)." <a target='_blank' href='".get_option('mm_facebook_page')."'>...</a></li>";

			$comments = "";
			if ($data_feed->{'comments'}->{'count'} <> "0") {
				$username = $data_feed->{'comments'}->{'data'}[0]->{'from'}->{'name'};
					$username = str_replace(" ", ".", $username);
					$comments = "<ul><li><img src='http://graph.facebook.com/".$username."/picture'/>".facebook_TokenTruncate($data_feed->{'comments'}->{'data'}[0]->{'message'}, get_option('mm_facebook_chars_per_comment'))."</li></ul>";
			}  
	  	echo "<ul>".$fb_feed_content."</ul>";
	  	update_option("mm_facebook_recent_cached", "<ul>".$fb_feed_content."</ul>");
			update_option("mm_last_date_cached", date("d/m/y"));
	  } else {
			//facebook feed url
	    $url="http://www.facebook.com/feeds/page.php?id=".get_option("mm_facebook_id")."&format=atom10";
	    
	    //load and setup CURL
	    $c = curl_init();
	    
	    //set options and make it up to look like firefox
			$userAgent = "Firefox (WindowsXP) - Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6";
			curl_setopt($c, CURLOPT_USERAGENT, $userAgent);
			curl_setopt($c, CURLOPT_URL,$url);
			curl_setopt($c, CURLOPT_FAILONERROR, true);
			curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($c, CURLOPT_AUTOREFERER, true);
			curl_setopt($c, CURLOPT_RETURNTRANSFER,true);
			curl_setopt($c, CURLOPT_VERBOSE, false);     
	    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
	    
	    //get data from facebook and decode XML
	    $page = curl_exec($c);
	    $pxml= new SimpleXMLElement($page);

	    //close the connection
	    curl_close($c);

	    $myPosts = $pxml->entry;
	    $fb_feed_content = "";
	    
	    $fb_feed_content .= "<li><a target='_blank' href='".get_option("mm_facebook_page")."'><img src='".get_option('mm_facebook_img_profile_url')."'/></a>".facebookReplaceURLWithHTMLLinks(facebook_TokenTruncate($myPosts->content, get_option('mm_facebook_chars_per_post')), $caption)." <a target='_blank' href='".get_option('mm_facebook_page')."'>...</a></li>";
	    
			echo "<ul>".$fb_feed_content.$comments."</ul>";

			update_option("mm_facebook_recent_cached", "<ul>".$fb_feed_content.$comments."</ul>");
			update_option("mm_last_date_cached", date("d/m/y"));
	  }
	} else {
		echo(get_option("mm_facebook_recent_cached"));
	}

}


function most_recent_twitter_feed() {
			// jSON URL which should be requested
		$json_url = "https://api.twitter.com/1/statuses/user_timeline.json?include_entities=true&include_rts=true&screen_name=".get_option('mm_twitter_screen_name')."&count=".get_option('mm_twitter_max_posts');
		// Initializing curl
		$ch = curl_init( $json_url );
		 
		// Configuring curl options
		$options = array(
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_SSL_VERIFYPEER, false,
		CURLOPT_HTTPHEADER => array('Content-type: application/json')
		);

		// Setting curl options
		curl_setopt_array( $ch, $options );
		
		$twitter_feed_content = "";

		// Getting results
		$result =  curl_exec($ch); // Getting jSON result string
		if ($result) {
			$obj = json_decode($result);

			if (is_array($obj)) {
				$caption = "";
				$twitter_feed_content .= "<li><img src='".$obj[0]->{"user"}->{"profile_image_url"}."'/>".twitterReplaceURLWithHTMLLinks(facebook_TokenTruncate($obj[0]->{'text'}, get_option('mm_twitter_chars_per_post')), "link")."</li>";
			}

		}

		echo "<ul>".$twitter_feed_content."</ul>";
}


function facebook_TokenTruncate($string, $your_desired_width) {
	$string = strip_tags($string);
  $parts = preg_split('/([\s\n\r]+)/', $string, null, PREG_SPLIT_DELIM_CAPTURE);
  $parts_count = count($parts);

  $length = 0;
  $last_part = 0;
  for (; $last_part < $parts_count; ++$last_part) {
    $length += strlen($parts[$last_part]);
    if ($length > $your_desired_width) { break; }
  }

  return implode(array_slice($parts, 0, $last_part));
}

function twitterReplaceURLWithHTMLLinks($text, $caption) {
	$content = preg_replace('/(^|\s)#(\w*[a-zA-Z_]+\w*)/', '\1#<a href="http://search.twitter.com/search?q=%23\2">\2</a>', facebookReplaceURLWithHTMLLinks($text, $caption));
	$content = preg_replace('/(^|\s?)@(\w*[a-zA-Z_]+\w*)/', '\1@<a href="http://twitter.com/\2">\2</a>', $content);

	return $content;
}

function facebookReplaceURLWithHTMLLinks($text, $caption) {
	$url_text = "\\2";
	if ($caption <> "") {
	   $url_text = $caption;
	}
	$text = preg_replace(
	 array(
	   '/(^|\s|>)(www.[^<> \n\r]+)/iex',
	   '/(^|\s|>)([_A-Za-z0-9-]+(\\.[A-Za-z]{2,3})?\\.[A-Za-z]{2,4}\\/[^<> \n\r]+)/iex',
	   '/(?(?=<a[^>]*>.+<\/a>)(?:<a[^>]*>.+<\/a>)|([^="\']?)((?:https?):\/\/([^<> \n\r]+)))/iex'
	 ),  
	 array(
	   "stripslashes((strlen('\\2')>0?'\\1<a href=\"http://\\2\" target=\"_blank\">$url_text</a>&nbsp;\\3':'\\0'))",
	   "stripslashes((strlen('\\2')>0?'\\1<a href=\"http://\\2\" target=\"_blank\">$url_text</a>&nbsp;\\4':'\\0'))",
	   "stripslashes((strlen('\\2')>0?'\\1<a href=\"\\2\" target=\"_blank\">$url_text</a>&nbsp;':'\\0'))",
	 ),  
	 $text
	);
	return $text;
}

function social_feed_style() {
	wp_register_style( 'my-social-feed-style', plugins_url('social.php', __FILE__) );
  wp_enqueue_style('my-social-feed-style');
}

add_filter('wp_head', 'social_feed_style');

function check_social_feed_dependencies_are_active($plugin_name, $dependencies) {
  $msg_content = "<div class='updated'><p>Sorry for the confusion but you must install and activate ";
  $plugins_array = array();
  $upgrades_array = array();
  define('PLUGINPATH', ABSPATH.'wp-content/plugins');
  foreach ($dependencies as $key => $value) {
    $plugin = get_plugin_data(PLUGINPATH."/".$value["plugin"],true,true);
    $url = $value["url"];
    if (!is_plugin_active($value["plugin"])) {
      array_push($plugins_array, $key);
    } else {
      if (isset($value["version"]) && str_replace(".", "", $plugin["Version"]) < str_replace(".", "", $value["version"])) {
        array_push($upgrades_array, $key);
      }
    }
  }
  $msg_content .= implode(", ", $plugins_array) . " before you can use $plugin_name. Please go to Plugins/Add New and search/install the following plugin(s): ";
  $download_plugins_array = array();
  foreach ($dependencies as $key => $value) {
    if (!is_plugin_active($value["plugin"])) {
      $url = $value["url"];
      array_push($download_plugins_array, $key);
    }
  }
  $msg_content .= implode(", ", $download_plugins_array)."</p></div>";
  if (count($plugins_array) > 0) {
    deactivate_plugins( __FILE__, true);
    echo($msg_content);
  } 

  if (count($upgrades_array) > 0) {
    deactivate_plugins( __FILE__,true);
    echo "<div class='updated'><p>$plugin_name requires the following plugins to be updated: ".implode(", ", $upgrades_array).".</p></div>";
  }
}

?>