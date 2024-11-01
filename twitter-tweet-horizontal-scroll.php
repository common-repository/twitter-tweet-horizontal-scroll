<?php

/*
Plugin Name: Twitter tweet horizontal scroll
Plugin URI: http://www.gopiplus.com/work/2012/12/19/twitter-tweet-horizontal-scroll-wordpress-plugin/
Description: Twitter tweet scroll is a simple plugin to show (scroll) your most recent tweet into your wordpress website. 
Author: Gopi.R
Version: 1.0
Author URI: http://www.gopiplus.com/work/2012/12/19/twitter-tweet-horizontal-scroll-wordpress-plugin/
Donate link: http://www.gopiplus.com/work/2012/12/19/twitter-tweet-horizontal-scroll-wordpress-plugin/
Tags: twitter, tweet, horizontal, scroll
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

global $wpdb, $wp_version;
define("TwitterRealURL", "http://api.twitter.com/1/statuses/user_timeline.rss?screen_name=##USERNAME##");

function twittertweet_add_javascript_files() 
{
	if (!is_admin())
	{
		wp_enqueue_script( 'jquery');
		wp_enqueue_script( 'jquery.cycle.all.min', get_option('siteurl').'/wp-content/plugins/twitter-tweet-horizontal-scroll/script/jquery.cycle.all.min.js');
		wp_enqueue_style( 'twitter-tweet', get_option('siteurl').'/wp-content/plugins/twitter-tweet-horizontal-scroll/script/twitter-tweet-horizontal-scroll.css');
	}	
}

function twittertweet_activation() 
{
	global $wpdb;
	add_option('twittertweet_title', "Twitter tweet scroll");
	add_option('twittertweet_username', "gopiplus");
	add_option('twittertweet_direction', "Left");
}

function twittertweet_admin_options() 
{
	global $wpdb;
	?>
	<div class="wrap">
    <h2>Twitter tweet horizontal scroll (Widget Setting)</h2>
    </div>
	<?php
	$twittertweet_title = get_option('twittertweet_title');
	$twittertweet_username = get_option('twittertweet_username');
	$twittertweet_direction = get_option('twittertweet_direction');
	
	if (@$_POST['twittertweet_submit']) 
	{
		$twittertweet_title = stripslashes($_POST['twittertweet_title']);
		$twittertweet_username = stripslashes($_POST['twittertweet_username']);
		$twittertweet_direction = stripslashes($_POST['twittertweet_direction']);
		
		update_option('twittertweet_title', $twittertweet_title );
		update_option('twittertweet_username', $twittertweet_username );
		update_option('twittertweet_direction', $twittertweet_direction );
	}
	
	?>
	<form name="twittertweet_form" method="post" action="">
	<?php
	echo '<p>Widget title:<br><input  style="width: 200px;" type="text" value="';
	echo $twittertweet_title . '" name="twittertweet_title" id="twittertweet_title" /></p>';
	
	echo '<p>Twitter username:<br>@<input  style="width: 200px;" type="text" value="';
	echo $twittertweet_username . '" name="twittertweet_username" id="twittertweet_username" /></p>';
	
	echo '<p>Scroll direction:<br><input  style="width: 212px;" type="text" value="';
	echo $twittertweet_direction . '" name="twittertweet_direction" id="twittertweet_direction" /> (Left / Right / Up / Down) </p>';
	
	echo '<input name="twittertweet_submit" id="twittertweet_submit" lang="publish" class="button-primary" value="Update Setting" type="Submit" />';
	?>
	</form>
	<br />
	<div class="wrap">
	<strong>Plugin Configuration</strong>
	<ol>
		<li>Drag and drop the widget</li>
		<li>Add directly in the theme</li>
		<li>Short code for pages and posts</li>
	</ol>
	Check official website for more information <a href="http://www.gopiplus.com/work/2012/12/19/twitter-tweet-horizontal-scroll-wordpress-plugin/" target="_blank">click here</a>
	</div>
	<?php
}

function twittertweet_shortcode( $atts ) 
{
	global $wpdb;
	
	global $Settings1;
	global $Settings2;
	if (!isset($Settings1) || $Settings1 !== true)
	{
		$Settings1 = true;
		$Setting = "1";
	}
	elseif (!isset($Settings2) || $Settings2 !== true)
	{
		$Settings2 = true;
		$Setting = "2";
	}
	else
	{
		$Setting = "3";
	}
	
	//[twitter-tweet username="gopiplus" direction="Left"]
	if ( ! is_array( $atts ) )
	{
		return '';
	}
	$username = trim($atts['username']);
	$direction = trim($atts['direction']);
	
	$url = str_replace("##USERNAME##", $username, TwitterRealURL);
	
	$xml = "";
	$twittertweet = "";
	$cnt = 0;
	$textlength  = 200;
	$f = fopen( $url, 'r' );
	while( $data = fread( $f, 4096 ) ) { $xml .= $data; }
	fclose( $f );
	preg_match_all( "/\<item\>(.*?)\<\/item\>/s", $xml, $itemblocks );

	if ( ! empty($itemblocks) ) 
	{
		$twittertweet = $twittertweet . '<div id="twittertweet'.$Setting.'">';
		foreach( $itemblocks[1] as $block )
		{
			preg_match_all( "/\<title\>(.*?)\<\/title\>/",  $block, $title );
			preg_match_all( "/\<link\>(.*?)\<\/link\>/", $block, $link );
			preg_match_all( "/\<description\>(.*?)\<\/description\>/", $block, $description );
			
			$twittertweet_title = $title[1][0];
			$twittertweet_title = addslashes(trim($twittertweet_title));
			$twittertweet_link = $link[1][0];
			$twittertweet_link = trim($twittertweet_link);
			$twittertweet_text = addslashes(trim($description[1][0]));
			$twittertweet_text = str_replace("&lt;![CDATA[","",$twittertweet_text);
			$twittertweet_text = str_replace("<![CDATA[","",$twittertweet_text);
			$twittertweet_text = str_replace("]]&gt;","",$twittertweet_text);
			$twittertweet_text = str_replace("]]>","",$twittertweet_text);
			if(is_numeric($textlength))
			{
				if($textlength <> "" && $textlength > 0 )
				{
					$twittertweet_text = substr($twittertweet_text, 0, $textlength);
				}
			}
			$twittertweet_text = preg_replace('/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i', '', $twittertweet_text);
			$twittertweet_text = str_replace($username.":", '', $twittertweet_text);
			$rest = substr(trim($twittertweet_text), -1); 
			if ($rest == ":")
			{
				$twittertweet_text = substr(trim($twittertweet_text), 0, -1);
			}
			if($twittertweet_text <> "" )
			{		
				$twittertweet = $twittertweet . '<p><a target="_blank" href="'.$twittertweet_link.'">'.$twittertweet_text.'</a></p>';
			}
		}
		$twittertweet = $twittertweet . '</div>';
		$twittertweet = $twittertweet . '<script type="text/javascript">';
		$twittertweet = $twittertweet . 'jQuery(function() {';
		$twittertweet = $twittertweet . "jQuery('#twittertweet".$Setting."').cycle({fx: 'scroll".$direction."',speed: 700,timeout: 5000";
		$twittertweet = $twittertweet . '});';
		$twittertweet = $twittertweet . '});';
		$twittertweet = $twittertweet . '</script>';
	}
	else
	{
		$twittertweet = "Please check your twitter username";
	}
	return $twittertweet;
}

function twittertweet_add_to_menu() 
{
	if (is_admin()) 
	{
		add_options_page('Twitter tweet horizontal scroll', 'Twitter tweet horizontal scroll', 'manage_options', __FILE__, 'twittertweet_admin_options' );
	}
}

function twittertweet_deactivation() 
{
	// No action required.
}

function twittertweet($username = 'gopiplus') 
{
	$arr = array();
	$arr["username"] = trim($username);
	$arr["direction"] = trim(get_option('twittertweet_direction'));
	echo twittertweet_shortcode($arr);
}

function twittertweet_widget($args) 
{
	extract($args);
	echo $before_widget . $before_title;
	echo get_option('twittertweet_title');
	echo $after_title;
	
	// [twitter-real-scrolling username="gopiplus" direction="Left"]
	$arr = array();
	$arr["username"] = trim(get_option('twittertweet_username'));
	$arr["direction"] = trim(get_option('twittertweet_direction'));
	echo twittertweet_shortcode($arr);
	
	echo $after_widget;
}

function twittertweet_control()
{
	echo 'Twitter tweet horizontal scroll';
}

function twittertweet_init()
{
	if(function_exists('wp_register_sidebar_widget')) 
	{
		wp_register_sidebar_widget('twitter-tweet-horizontal-scroll', 'Twitter tweet horizontal scroll', 'twittertweet_widget');
	}
	
	if(function_exists('wp_register_widget_control')) 
	{
		wp_register_widget_control('twitter-tweet-horizontal-scroll', array('Twitter tweet horizontal scroll', 'widgets'), 'twittertweet_control');
	}
}

add_action("plugins_loaded", "twittertweet_init");
add_shortcode( 'twitter-tweet', 'twittertweet_shortcode' );
register_activation_hook(__FILE__, 'twittertweet_activation');
register_deactivation_hook(__FILE__, 'twittertweet_deactivation');
add_action('admin_menu', 'twittertweet_add_to_menu');
add_action('wp_enqueue_scripts', 'twittertweet_add_javascript_files');
?>