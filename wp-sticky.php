<?php
/*
Plugin Name: WP-Sticky
Plugin URI: http://lesterchan.net/portfolio/programming/php/
Description: Adds a sticky post feature to your WordPress's blog. Modified from Adhesive by Owen Winkler.
Version: 1.50
Author: Lester 'GaMerZ' Chan
Author URI: http://lesterchan.net
*/


/*  
	Copyright 2009  Lester Chan  (email : lesterchan@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


### Create Text Domain For Translations
add_action('init', 'sticky_textdomain');
function sticky_textdomain() {
	load_plugin_textdomain('wp-sticky', false, 'wp-sticky');
}


### Sticky Table Name
global $wpdb;
$wpdb->sticky = $wpdb->prefix.'sticky';


### Function: Sticky Option Menu
add_action('admin_menu', 'sticky_menu');
function sticky_menu() {
	if (function_exists('add_options_page')) {
		add_options_page(__('WP-Sticky', 'wp-sticky'), __('WP-Sticky', 'wp-sticky'), 'manage_options', 'wp-sticky/sticky-options.php');
	}
}


### Function: Get Sticky Option
function get_sticky_option($option) {
	$sticky_options = get_option('sticky_options');
	return $sticky_options[$option];
}


### Function: Check Whether In Category
if(!function_exists('check_in_category')) {
	function check_in_category() {
		$category_base = get_option('category_base');
		if(empty($category_base)) {
			$category_base = '/category';
		}
		if(strpos($_SERVER['REQUEST_URI'], $category_base) !== false || intval($_GET['cat']) > 0) {
			return true;
		}
		return false;
	}
}


### Function: Check For Pages To Disable Sticky
function disable_sticky() {
	$pages = array('edit-pages.php');
	foreach($pages as $page) {
		if(strpos($_SERVER['REQUEST_URI'], $page) !== false) {
			return true;
		}
	}
}


### Function: Sticky Query
if(!disable_sticky()) {
	if(intval(get_sticky_option('category_only')) == 1) {
		if(check_in_category()) {
			add_filter('posts_fields', 'sticky_fields');
			//add_filter('posts_join', 'sticky_join');
			add_filter('posts_join_paged', 'sticky_join');
			add_filter('posts_orderby', 'sticky_orderby', 1);
		}
	} else {
		add_filter('posts_fields', 'sticky_fields');
		//add_filter('posts_join', 'sticky_join');
		add_filter('posts_join_paged', 'sticky_join');
		add_filter('posts_orderby', 'sticky_orderby', 1);
	}
}
function sticky_fields($content) {
	global $wpdb;
	$content .= ", $wpdb->sticky.sticky_status";
	return $content;
}
function sticky_join($content) {
	global $wpdb;
	$content .= " LEFT JOIN $wpdb->sticky ON $wpdb->sticky.sticky_post_id = $wpdb->posts.ID";
	return $content;
}
function sticky_orderby($content) {
	global $wpdb;
	$content = "($wpdb->sticky.sticky_status = 2 AND $wpdb->sticky.sticky_status IS NOT NULL) DESC, DATE_FORMAT($wpdb->posts.post_date,'%Y-%m-%d') DESC, ($wpdb->sticky.sticky_status = 1 AND $wpdb->sticky.sticky_status IS NULL) DESC, DATE_FORMAT($wpdb->posts.post_date,'%T') DESC";
	return $content;
}


### Function: Output Post Sticky Status, Sticky Or Announcement
function post_sticky_status($before = '', $after = '', $display = true) {
	global $id, $post;
	$temp = '';
	switch($post->sticky_status) {
		case 1:
			$temp = $before.__('Sticky', 'wp-sticky').$after;
			break;
		case 2:
			$temp = $before.__('Announcement', 'wp-sticky').$after;
			break;
	}
	if($display) {
		echo $temp;
	} else {
		return $temp;
	}
}


### Function: If Sticky Condition
function is_stickied() {
	global $id, $post;
	if($post->sticky_status == 1) {
		return true;
	} else {
		return false;
	}
}


### Function: If Announcement Condition
function is_announcement() {
	global $id, $post;
	if($post->sticky_status == 2) {
		return true;
	} else {
		return false;
	}
}


### Function: Display Announcement Banner
function announcement_banner($display = true) {
	global $post, $printed_announcement;
	if($post->sticky_status == 2 && intval(get_sticky_option('display_date')) == 0) {
		if($printed_announcement) {
			return;
		} else {
			$printed_announcement = true;
			if($display) {
				echo get_sticky_option('announcement_banner');
			} else {
				return get_sticky_option('announcement_banner');
			}
		}
	}
	return $content;
}


### Function: Sticky The Date
add_filter('the_date', 'sticky_the_date', 1000);
function sticky_the_date($content) {
	global $post, $previousday, $printed_announcement;
	if($post->sticky_status == 2 && intval(get_sticky_option('display_date')) == 0) {
		$previousday = '';
		if($printed_announcement) {
			return;
		} else {
			$printed_announcement = true;
			return get_sticky_option('announcement_banner');
		}
	}
	return $content;
}


### Function: Sticky The Title
add_filter('the_title', 'sticky_the_title');
function sticky_the_title($content) {
	global $post;
	if(strpos($_SERVER['REQUEST_URI'], '/edit.php') !== false && ($post->sticky_status > 0)) {
		 $content = post_sticky_status('', '', false).': '.$content;
	}
	return $content;
}


### Function: Sticky The Content
add_filter('the_content', 'sticky_the_content');
function sticky_the_content($content) {
	global $post;
	switch($post->sticky_status) {
		case 1:
			return '<div class="sticky_post">'.$content.'</div>'."\n";
			break;
		case 2:
			return '<div class="announcement_post">'.$content.'</div>'."\n";
			break;
		default:
			return $content;
	}
}


### Function: Processing Sticky Post
add_action('save_post', 'add_sticky_admin_process');
function add_sticky_admin_process($post_ID) {
	global $wpdb;
	if(!wp_is_post_revision($post_ID)) {
		$post_status_sticky_status = intval($_POST['post_status_sticky']);
		// Normal Posts
		if($post_status_sticky_status == 0 && intval($post_ID) > 0) {
			$wpdb->query("DELETE FROM $wpdb->sticky WHERE sticky_post_id = $post_ID");
		// Sticky Post/Announcement Post
		} else {
			// Ensure No Duplicate Field
			$check = intval($wpdb->get_var("SELECT sticky_status FROM $wpdb->sticky WHERE sticky_post_id = $post_ID"));
			if($check == 0) {
				$wpdb->query("INSERT INTO $wpdb->sticky VALUES($post_ID, $post_status_sticky_status)");
			} else {
				$wpdb->query("UPDATE $wpdb->sticky SET sticky_status = $post_status_sticky_status WHERE sticky_post_id = $post_ID");
			}
		}
	}
}


### Function: Delete Away Sticky If Post Is Deleted
add_action('delete_post', 'delete_sticky_admin_process');
function delete_sticky_admin_process($post_ID) {
	global $wpdb;
	if(!wp_is_post_revision($post_ID)) {
		$wpdb->query("DELETE FROM $wpdb->sticky WHERE sticky_post_id = $post_ID");
	}
}


### Function: Add Meta Box To WP >= 2.5 Admin
function sticky_metabox_admin() {
	global $wpdb;
	$edit_post = intval($_GET['post']);
	$post_status_sticky_id = 0;
	$post_status_sticky_status  = 0;
	if($edit_post > 0) {
		$post_status_sticky_status = intval($wpdb->get_var("SELECT sticky_status FROM $wpdb->sticky WHERE sticky_post_id = $edit_post"));
	}
?>
	<label for="post_status_announcement" class="selectit"><input type="radio" id="post_status_announcement" name="post_status_sticky" value="2"<?php checked($post_status_sticky_status, 2); ?>/>&nbsp;<?php _e('Announcement', 'wp-sticky'); ?></label>
	<br />
	<label for="post_status_sticky" class="selectit"><input type="radio" id="post_status_sticky" name="post_status_sticky" value="1"<?php checked($post_status_sticky_status, 1); ?>/>&nbsp;<?php _e('Sticky', 'wp-sticky'); ?></label>
	<br />
	<label for="post_status_normal" class="selectit"><input type="radio" id="post_status_normal" name="post_status_sticky" value="0"<?php checked($post_status_sticky_status, 0); ?>/>&nbsp;<?php _e('Normal', 'wp-sticky'); ?></label>
<?php
}


### Function: Add Meta/DBX Box
add_action('admin_menu', 'sticky_add_meta_box');
function sticky_add_meta_box() {
	add_meta_box('poststickystatusdiv', __('Post Sticky Status', 'wp-sticky'), 'sticky_metabox_admin', 'post', 'side');
}
 

### Function: Sticky Init
add_action('activate_wp-sticky/wp-sticky.php', 'sticky_init');
function sticky_init() {
	global $wpdb;
	if(@is_file(ABSPATH.'/wp-admin/upgrade-functions.php')) {
		include_once(ABSPATH.'/wp-admin/upgrade-functions.php');
	} elseif(@is_file(ABSPATH.'/wp-admin/includes/upgrade.php')) {
		include_once(ABSPATH.'/wp-admin/includes/upgrade.php');
	} else {
		die('We have problem finding your \'/wp-admin/upgrade-functions.php\' and \'/wp-admin/includes/upgrade.php\'');
	}
	// Create Sticky Table
	$create_sticky_sql = "CREATE TABLE $wpdb->sticky (".
								"sticky_post_id bigint(20) NOT NULL,".
								"sticky_status tinyint(1) NOT NULL default '0',".
								"PRIMARY KEY (sticky_post_id))";
	maybe_create_table($wpdb->sticky, $create_sticky_sql);
	// Add Options
	$sticky_options = array();
	$sticky_options['category_only'] = 0;
	$sticky_options['display_date'] = 0;
	$sticky_options['announcement_banner'] = __('Announcement', 'wp-sticky');
	add_option('sticky_options', $sticky_options, 'Sticky Options');
}
?>