<?php
/*
+----------------------------------------------------------------+
|																							|
|	WordPress 2.7 Plugin: WP-Sticky 1.50										|
|	Copyright (c) 2009 Lester "GaMerZ" Chan									|
|																							|
|	File Written By:																	|
|	- Lester "GaMerZ" Chan															|
|	- http://lesterchan.net															|
|																							|
|	File Information:																	|
|	- WP-Sticky Options																|
|	- wp-content/plugins/wp-sticky/sticky-options.php					|
|																							|
+----------------------------------------------------------------+
*/


### Check Whether User Can Manage Sticky Options
if(!current_user_can('manage_options')) {
	die('Access Denied');
}


### Variables
$base_name = plugin_basename('wp-sticky/sticky-options.php');
$base_page = 'admin.php?page='.$base_name;
$mode = trim($_GET['mode']);
$sticky_tables = array($wpdb->sticky);
$sticky_settings = array('sticky_options');


### Form Processing
// Update Options
if(!empty($_POST['Submit'])) {
	check_admin_referer('wp-sticky_options');
	$text = '';
	$sticky_options['display_date'] = intval($_POST['display_date']);
	$sticky_options['category_only'] = intval($_POST['category_only']);
	$sticky_options['announcement_banner'] = trim($_POST['announcement_banner']);		
	$update_sticky_options = update_option('sticky_options', $sticky_options);
	if($update_sticky_options) {
		$text = '<font color="green">'.__('Sticky Options Updated', 'wp-sticky').'</font>';
	}
	if(empty($text)) {
		$text = '<font color="red">'.__('No Sticky Option Updated', 'wp-sticky').'</font>';
	}
}
// Uninstall WP-Sticky
if(!empty($_POST['do'])) {
	switch($_POST['do']) {		
		case __('UNINSTALL WP-Sticky', 'wp-sticky') :
			if(trim($_POST['uninstall_sticky_yes']) == 'yes') {
				echo '<div id="message" class="updated fade">';
				echo '<p>';
				foreach($sticky_tables as $table) {
					$wpdb->query("DROP TABLE {$table}");
					echo '<font style="color: green;">';
					printf(__('Table \'%s\' has been deleted.', 'wp-sticky'), "<strong><em>{$table}</em></strong>");
					echo '</font><br />';
				}
				echo '</p>';
				echo '<p>';
				foreach($sticky_settings as $setting) {
					$delete_setting = delete_option($setting);
					if($delete_setting) {
						echo '<font color="green">';
						printf(__('Setting Key \'%s\' has been deleted.', 'wp-sticky'), "<strong><em>{$setting}</em></strong>");
						echo '</font><br />';
					} else {
						echo '<font color="red">';
						printf(__('Error deleting Setting Key \'%s\'.', 'wp-sticky'), "<strong><em>{$setting}</em></strong>");
						echo '</font><br />';
					}
				}
				echo '</p>';
				echo '</div>'; 
				$mode = 'end-UNINSTALL';
			}
			break;
	}
}


### Determines Which Mode It Is
switch($mode) {
		//  Deactivating WP-Sticky
		case 'end-UNINSTALL':
			$deactivate_url = 'plugins.php?action=deactivate&amp;plugin=wp-sticky/wp-sticky.php';
			if(function_exists('wp_nonce_url')) { 
				$deactivate_url = wp_nonce_url($deactivate_url, 'deactivate-plugin_wp-sticky/wp-sticky.php');
			}
			echo '<div class="wrap">';
			echo '<h2>'.__('Uninstall WP-Sticky', 'wp-sticky').'</h2>';
			echo '<p><strong>'.sprintf(__('<a href="%s">Click Here</a> To Finish The Uninstallation And WP-Sticky Will Be Deactivated Automatically.', 'wp-sticky'), $deactivate_url).'</strong></p>';
			echo '</div>';
			break;
	// Main Page
	default:
		$sticky_options = get_option('sticky_options');
?>
<?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.$text.'</p></div>'; } ?>
<!-- Sticky Options -->
<form method="post" action="<?php echo admin_url('admin.php?page='.plugin_basename(__FILE__)); ?>">
<?php wp_nonce_field('wp-sticky_options'); ?>
<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php _e('Sticky Options', 'wp-sticky'); ?></h2>
	<table class="form-table">
		<tr>
			<th scope="row" valign="top"><?php _e('Categories Only:', 'wp-sticky'); ?></th>
			<td>
				<select name="category_only">
					<option value="0"<?php selected('0', $sticky_options['category_only']); ?>><?php _e('No', 'wp-sticky'); ?></option>
					<option value="1"<?php selected('1', $sticky_options['category_only']); ?>><?php _e('Yes', 'wp-sticky'); ?></option>
				</select>
				<br /><?php _e('Display announcement and sticky posts only when viewing categories.', 'wp-sticky'); ?>
			</td>
		</tr>
		<tr>
			<th scope="row" valign="top"><?php _e('Display Date:', 'wp-sticky'); ?></th>
			<td>
				<select name="display_date">
					<option value="0"<?php selected('0', $sticky_options['display_date']); ?>><?php _e('No', 'wp-sticky'); ?></option>
					<option value="1"<?php selected('1', $sticky_options['display_date']); ?>><?php _e('Yes', 'wp-sticky'); ?></option>
				</select>
				<br /><?php _e('Displays the date instead of the <strong>Announcement Banner</strong> on announcement posts.', 'wp-sticky'); ?>
			</td>
		</tr>
		<tr>
			<th scope="row" valign="top"><?php _e('Announcement Banner:', 'wp-sticky'); ?></th>
			<td>
				<input type="text" name="announcement_banner" size="60" value="<?php echo htmlspecialchars($sticky_options['announcement_banner']); ?>" />
				<br /><?php _e('This banner is displayed instead of the date if you choose \'No\' for <strong>Display Date</strong>.', 'wp-sticky'); ?>
			</td>
		</tr>
	</table>
	<p class="submit">
		<input type="submit" name="Submit" class="button" value="<?php _e('Save Changes', 'wp-sticky'); ?>" />
	</p>
</div>
</form>
<p>&nbsp;</p>

<!-- Uninstall WP-Sticky -->
<form method="post" action="<?php echo admin_url('admin.php?page='.plugin_basename(__FILE__)); ?>">
<div class="wrap"> 
	<h3><?php _e('Uninstall WP-Sticky', 'wp-sticky'); ?></h3>
	<p style="text-align: left;">
		<?php _e('Deactivating WP-Sticky plugin does not remove any data that may have been created, such as the sticky options. To completely remove this plugin, you can uninstall it here.', 'wp-sticky'); ?>
	</p>
	<p style="text-align: left; color: red">
		<strong><?php _e('WARNING:', 'wp-sticky'); ?></strong><br />
		<?php _e('Once uninstalled, this cannot be undone. You should use a Database Backup plugin of WordPress to back up all the data first.', 'wp-sticky'); ?>
	</p>
	<p style="text-align: left; color: red">
		<strong><?php _e('The following WordPress Options/Tables will be DELETED:', 'wp-sticky'); ?></strong><br />
	</p>
	<table class="widefat">
		<thead>
			<tr>
				<th><?php _e('WordPress Options', 'wp-sticky'); ?></th>
				<th><?php _e('WordPress Tables', 'wp-sticky'); ?></th>
			</tr>
		</thead>
		<tr>
			<td valign="top">
				<ol>
				<?php
					foreach($sticky_settings as $settings) {
						echo '<li>'.$settings.'</li>'."\n";
					}
				?>
				</ol>
			</td>
			<td valign="top" class="alternate">
				<ol>
				<?php
					foreach($sticky_tables as $tables) {
						echo '<li>'.$tables.'</li>'."\n";
					}
				?>
				</ol>
			</td>
		</tr>
	</table>
	<p>&nbsp;</p>
	<p style="text-align: center;">
		<input type="checkbox" name="uninstall_sticky_yes" value="yes" />&nbsp;<?php _e('Yes', 'wp-sticky'); ?><br /><br />
		<input type="submit" name="do" value="<?php _e('UNINSTALL WP-Sticky', 'wp-sticky'); ?>" class="button" onclick="return confirm('<?php _e('You Are About To Uninstall WP-Sticky From WordPress.\nThis Action Is Not Reversible.\n\n Choose [Cancel] To Stop, [OK] To Uninstall.', 'wp-sticky'); ?>')" />
	</p>
</div> 
</form>
<?php
} // End switch($mode)
?>