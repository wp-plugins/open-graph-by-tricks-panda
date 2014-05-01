<?php

function tricks_panda_og_register_settings()
{
	add_option('tricks_panda_og_default');
	add_option('tricks_panda_og_site_type', 'website');
	add_option('tricks_panda_og_fb_page');
	add_option('tricks_panda_og_app_id');
	add_option('tricks_panda_og_home_desc');
	register_setting('tpog', 'tricks_panda_og_default');
	register_setting('tpog', 'tricks_panda_og_site_type');
	register_setting('tpog', 'tricks_panda_og_fb_page');
	register_setting('tpog', 'tricks_panda_og_app_id');
	register_setting('tpog', 'tricks_panda_og_home_desc');
}

add_action('admin_init', 'tricks_panda_og_register_settings');

function tricks_panda_og_register_options_page()
{
	add_options_page('Open Graph', 'Open Graph', 'manage_options', 'open-graph-options', 'open_graph_options_page');
}

add_action('admin_menu', 'tricks_panda_og_register_options_page');

function open_graph_options_page()
{
?>
<div class="wrap">
	<?php
	screen_icon(); ?>
	<h2>Open Graph By Tricks Panda</h2>
	<form method="post" action="options.php"> 
		<?php
	settings_fields('tpog'); ?>
		<h3>You can configure Open Graph settings from the form below:</h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row" style="width:350px;"><label for="tricks_panda_og_default">Website Logo (Default Image):</label></th>
					<td><input type="text" style="width:429px;" id="tricks_panda_og_default" name="tricks_panda_og_default" value="<?php
	echo get_option('tricks_panda_og_default'); ?>"/></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="tricks_panda_og_site_type">Site Type:</label></th>
					<td><select id="tricks_panda_og_site_type" name="tricks_panda_og_site_type" value=" <?php
	echo get_option('tricks_panda_og_site_type'); ?>">
					    <option value="website" <?php
	if (get_option('tricks_panda_og_site_type') == website) echo 'selected="selected"'; ?>>Website</option>
					    <option value="blog" <?php
	if (get_option('tricks_panda_og_site_type') == blog) echo 'selected="selected"'; ?>>Blog</option>
					  </select></td>
				</tr>
				<tr valign="top">
					<th scope="row" style="width:350px;"><label for="tricks_panda_og_fb_page">Facebook Page URL:</label></th>
					<td><input type="text" style="width:429px;" id="tricks_panda_og_fb_page" name="tricks_panda_og_fb_page" value="<?php
	echo get_option('tricks_panda_og_fb_page'); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row" style="width:350px;"><label for="tricks_panda_og_app_id">Facebook APP ID:</label></th>
					<td><input type="text" style="width:429px;" id="tricks_panda_og_app_id" name="tricks_panda_og_app_id" value="<?php
	echo get_option('tricks_panda_og_app_id'); ?>" /></td>
				</tr>
			</table>

		<h3>Additional options:</h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row" style="width:350px;"><label for="tricks_panda_og_home_desc">Homepage Description:</label></th>
					<td><textarea type="text" style="width:429px; height:100px;" id="tricks_panda_og_home_desc" name="tricks_panda_og_home_desc"><?php echo get_option('tricks_panda_og_home_desc'); ?></textarea></td>
				</tr>
			</table>
		<?php
	submit_button(); ?>
	</form>
</div>
<?php
}

?>