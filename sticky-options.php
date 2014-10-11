<?php
### Check Whether User Can Manage Sticky Options
if( ! current_user_can( 'manage_options' )) {
	die( 'Access Denied' );
}

### Variables
$base_name = plugin_basename( 'wp-sticky/sticky-options.php' );
$base_page = 'admin.php?page='.$base_name;

### Form Processing
if( ! empty( $_POST['Submit'] ) ) {
	check_admin_referer( 'wp-sticky_options' );
	$text = '';
	$sticky_options['display_date'] = intval( $_POST['display_date'] );
	$sticky_options['display_before_title'] = intval( $_POST['display_before_title'] );
	$sticky_options['category_only'] = intval( $_POST['category_only'] );
	$sticky_options['announcement_banner'] = trim( $_POST['announcement_banner'] );
	$update_sticky_options = update_option( 'sticky_options', $sticky_options );
	if( $update_sticky_options ) {
		$text = '<p style="color: green">'.__( 'Sticky Options Updated', 'wp-sticky' ).'</p>';
	}
	if( empty( $text ) ) {
		$text = '<p style="color: red">'.__( 'No Sticky Option Updated', 'wp-sticky' ).'</p>';
	}
}

$sticky_options = get_option( 'sticky_options' );

if( ! isset( $sticky_options['category_only'] ) ) {
	$sticky_options['category_only'] = 0;
}
if( ! isset( $sticky_options['display_date'] ) ) {
	$sticky_options['display_date'] = 0;
}
if( ! isset( $sticky_options['display_before_title'] ) ) {
	$sticky_options['display_before_title'] = 1;
}
if( ! isset( $sticky_options['announcement_banner'] ) ) {
	$sticky_options['announcement_banner'] = __( 'Announcement', 'wp-sticky' );
}
?>
<?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.$text.'</p></div>'; } ?>
<!-- Sticky Options -->
<form method="post" action="<?php echo admin_url( 'admin.php?page='.plugin_basename( __FILE__ ) ); ?>">
<?php wp_nonce_field( 'wp-sticky_options' ); ?>
<div class="wrap">
	<h2><?php _e( 'Sticky Options', 'wp-sticky' ); ?></h2>
	<table class="form-table">
		<tr>
			<th width="20%" scope="row" valign="top"><?php _e( 'Categories Only:', 'wp-sticky' ); ?></th>
			<td width="80%">
				<select name="category_only">
					<option value="0"<?php selected( 0, $sticky_options['category_only'] ); ?>><?php _e( 'No', 'wp-sticky' ); ?></option>
					<option value="1"<?php selected( 1, $sticky_options['category_only'] ); ?>><?php _e( 'Yes', 'wp-sticky' ); ?></option>
				</select>
				<br /><?php _e( 'Display announcement and sticky posts only when viewing categories.', 'wp-sticky' ); ?>
			</td>
		</tr>
		<tr>
			<th scope="row" valign="top"><?php _e( 'Display Date:', 'wp-sticky' ); ?></th>
			<td>
				<select name="display_date">
					<option value="0"<?php selected( 0, $sticky_options['display_date'] ); ?>><?php _e( 'No', 'wp-sticky' ); ?></option>
					<option value="1"<?php selected( 1, $sticky_options['display_date'] ); ?>><?php _e( 'Yes', 'wp-sticky' ); ?></option>
				</select>
				<br /><?php _e( 'Displays the date instead of the <strong>Announcement Banner</strong> on announcement posts.', 'wp-sticky' ); ?>
			</td>
		</tr>
		<tr>
			<th scope="row" valign="top"><?php _e( 'Display Sticky Status (\'Sticky\' or \'Announcement\' ) Before Post Title:', 'wp-sticky' ); ?></th>
			<td>
				<select name="display_date">
					<option value="0"<?php selected( 0, $sticky_options['display_before_title'] ); ?>><?php _e( 'No', 'wp-sticky' ); ?></option>
					<option value="1"<?php selected( 1, $sticky_options['display_before_title'] ); ?>><?php _e( 'Yes', 'wp-sticky' ); ?></option>
				</select>
				<br /><?php _e( 'Displays the date instead of the <strong>Announcement Banner</strong> on announcement posts.', 'wp-sticky' ); ?>
			</td>
		</tr>
		<tr>
			<th scope="row" valign="top"><?php _e( 'Announcement Banner:', 'wp-sticky' ); ?></th>
			<td>
				<input type="text" name="announcement_banner" size="60" value="<?php echo htmlspecialchars( stripslashes( $sticky_options['announcement_banner'] ) ); ?>" />
				<br /><?php _e( 'This banner is displayed instead of the date if you choose \'No\' for <strong>Display Date</strong>.', 'wp-sticky' ); ?>
			</td>
		</tr>
	</table>
	<p class="submit">
		<input type="submit" name="Submit" class="button" value="<?php _e( 'Save Changes', 'wp-sticky' ); ?>" />
	</p>
</div>
</form>