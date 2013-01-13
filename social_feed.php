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
}

function social_feed_settings_page() {
?>
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


// TODO: Need to create css file for social feed and link header.php to point to it.
function social_feed_style() {
	echo "<link rel='stylesheet' type='text/css' href='".get_option('siteurl')."/wp-content/plugins/social_feed/social.php' />";
}

add_filter('wp_head', 'social_feed_style');

?>
