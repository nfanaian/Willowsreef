<?php

if (!defined('ABSPATH'))
exit('<h1>Not Found</h1>The requested URL '.$_SERVER['SCRIPT_NAME'].' was not found on this server.'); //Exit if accessed directly

	if(!defined('FBCOMMENT')) return;
		
	function fbc_options_page() 
		{ 
			// If form was submitted
			if ( isset( $_POST['submitted'] ) ) 
			{			
				update_option('fbc_enabled', $_POST['fbc_enabled']);
				update_option('fbc_app_id', $_POST['fbc_app_id']);
				update_option('fbc_width', $_POST['fbc_width']);
				update_option('fbc_share_enabled', $_POST['fbc_share_enabled']);
				update_option('fbc_color_scheme', $_POST['fbc_color_scheme']);
				update_option('fbc_tw_enabled', $_POST['fbc_tw_enabled']);
				update_option('fbc_twitter', $_POST['fbc_twitter']);
				update_option('fbc_fb_enabled', $_POST['fbc_fb_enabled']);
				update_option('fbc_fbr_enabled', $_POST['fbc_fbr_enabled']);
				update_option('fbc_gp_enabled', $_POST['fbc_gp_enabled']);
				update_option('fbc_li_enabled', $_POST['fbc_li_enabled']);
				update_option('fbc_pn_enabled', $_POST['fbc_pn_enabled']);
				update_option('fbc_share_post', $_POST['fbc_share_post']);
				
				echo '<div id="message" class="updated fade"><p>' . __( 'Facebook Comment & Social Share options saved.', 'fbc' ) . '</p></div>';			
			}
								
			?>
			<div class="wrap woocommerce">
			<div class="icon32" id="icon-options-general"><br /></div>
			<h2><?php _e( 'FBC Options', 'fbc' ); ?></h2>
			
			
			<table width="90%" cellspacing="2">
			<tr>
				<td width="70%">
					<form action="" method="post">
					<table class="widefat fixed" cellspacing="0">
							<thead>
								<th width="30%"><?php _e('Option', 'fbc' ); ?></th>
								<th><?php _e('Setting', 'fbc' ); ?></th>
							</thead>
							<tbody>
								<tr>
									<td><label for="fbc_enabled"><?php _e('Enabled', 'fbc' ); ?></label></td>
									<td><input class="checkbox" name="fbc_enabled" id="fbc_enabled" value="yes" <?php if(get_option('fbc_enabled') == 'yes') echo 'checked="checked"';?> type="checkbox"> <?php _e('Check this if you will anable FB Comment & Social Sharing on your Woocommerce product page', 'fbc');?></td>
								</tr>
								<tr>
									<td><label for="fbc_app_id"><?php _e('Your Facebook App ID', 'fbc' ); ?></label></td>
									<td><input id="fbc_app_id" name="fbc_app_id" value="<?php echo get_option('fbc_app_id'); ?>" size="20"/><p class="description"><?php _e('You can get it at <a href="https://developers.facebook.com">https://developers.facebook.com</a>.', 'fbc')?></p></td>
								</tr>
								<tr>
									<td><label for="fbc_width"><?php _e('Width', 'fbc' ); ?></label></td>
									<td><input id="fbc_width" name="fbc_width" value="<?php echo get_option('fbc_width'); ?>" size="20"/></td>
								</tr>
								<tr>
									<td><label for="fbc_share_enabled"><?php _e('Facebook Comment Only?', 'fbc' ); ?></label></td>
									<td><input class="checkbox" name="fbc_share_enabled" id="fbc_share_enabled" value="yes" <?php if(get_option('fbc_share_enabled') == 'yes') echo 'checked="checked"';?> type="checkbox"> <?php _e('Check this if you will Show only Facebook Comment, Social Sharing will hide on your Woocommerce product page', 'fbc');?></td>
								</tr>
								<tr>
									<td><label for="fbc_color_scheme"><?php _e('Color Scheme', 'fbc' ); ?></label></td>
									<td>
										<select name="fbc_color_scheme" id="fbc_color_scheme">
												<option selected="selected" value="">-- <?php _e('Select Scheme', 'fbc' ); ?> --</option>
												<option value="light" <?php if(get_option('fbc_color_scheme') == 'light') echo 'selected="selected"';?>><?php _e('Light', 'fbc' ); ?></option>
												<option value="dark" <?php if(get_option('fbc_color_scheme') == 'dark') echo 'selected="selected"';?>><?php _e('Dark', 'fbc' ); ?></option>
										</select>
									</td>
								</tr>
								<tr>
									<td colspan="2"><b><?php _e('Social Share Setups', 'fbc');?></b></td>
								</tr>
								<tr>
									<td><label for="fbc_tw_enabled"><?php _e('Enable Twitter', 'fbc' ); ?></label></td>
									<td><input class="checkbox" name="fbc_tw_enabled" id="fbc_tw_enabled" value="yes" <?php if(get_option('fbc_tw_enabled') == 'yes') echo 'checked="checked"';?> type="checkbox"> <?php _e('Check this if you will anable Tweet twitter on your Woocommerce product page', 'fbc');?></td>
								</tr>
								<tr>
									<td><label for="fbc_share_post"><?php _e('Social Share Position', 'fbc' ); ?></label></td>
									<td>
									<select id="fbc_share_post" name="fbc_share_post">
										<option value="float" <?php if(get_option('fbc_share_post') == 'float') echo 'selected="selected"';?>><?php _e('Upper Image','wcqr');?></option>
										<option value="bottom" <?php if(get_option('fbc_share_post') == 'bottom') echo 'selected="selected"';?>><?php _e('Bellow Title','wcqr');?></option>
									</select>
									<p class="description"><?php _e('Position for Social Share.', 'wcqr')?></p></td>
								</tr>
								<tr>
									<td><label for="fbc_twitter"><?php _e('Your Twitter account', 'fbc' ); ?></label></td>
									<td><input id="fbc_twitter" name="fbc_twitter" value="<?php echo get_option('fbc_twitter'); ?>" size="20"/><p class="description"></p></td>
								</tr>
								<tr>
									<td><label for="fbc_fb_enabled"><?php _e('Enable Facebook Share', 'fbc' ); ?></label></td>
									<td><input class="checkbox" name="fbc_fb_enabled" id="fbc_fb_enabled" value="yes" <?php if(get_option('fbc_fb_enabled') == 'yes') echo 'checked="checked"';?> type="checkbox"> <?php _e('Check this if you will anable FB Share on your Woocommerce product page', 'fbc');?></td>
								</tr>
								<tr>
									<td><label for="fbc_fbr_enabled"><?php _e('Enable Facebook Recommended', 'fbc' ); ?></label></td>
									<td><input class="checkbox" name="fbc_fbr_enabled" id="fbc_fbr_enabled" value="yes" <?php if(get_option('fbc_fbr_enabled') == 'yes') echo 'checked="checked"';?> type="checkbox"> <?php _e('Check this if you will anable FB Recommended on your Woocommerce product page', 'fbc');?></td>
								</tr>
								<tr>
									<td><label for="fbc_gp_enabled"><?php _e('Enable Google Plus', 'fbc' ); ?></label></td>
									<td><input class="checkbox" name="fbc_gp_enabled" id="fbc_gp_enabled" value="yes" <?php if(get_option('fbc_gp_enabled') == 'yes') echo 'checked="checked"';?> type="checkbox"> <?php _e('Check this if you will anable google Plus on your Woocommerce product page', 'fbc');?></td>
								</tr>
								<tr>
									<td><label for="fbc_li_enabled"><?php _e('Enable Linkedin', 'fbc' ); ?></label></td>
									<td><input class="checkbox" name="fbc_li_enabled" id="fbc_li_enabled" value="yes" <?php if(get_option('fbc_li_enabled') == 'yes') echo 'checked="checked"';?> type="checkbox"> <?php _e('Check this if you will anable Linkedin on your Woocommerce product page', 'fbc');?></td>
								</tr>
								<tr>
									<td><label for="fbc_pn_enabled"><?php _e('Enable Pinterest', 'fbc' ); ?></label></td>
									<td><input class="checkbox" name="fbc_pn_enabled" id="fbc_pn_enabled" value="yes" <?php if(get_option('fbc_pn_enabled') == 'yes') echo 'checked="checked"';?> type="checkbox"> <?php _e('Check this if you will anable Pinterest on your Woocommerce product page', 'fbc');?></td>
								</tr>
								<tr>
									<td colspan=2">
										<input class="button-primary" type="submit" name="Save" value="<?php _e('Save Options', 'fbc' ); ?>" id="submitbutton" />
										<input type="hidden" name="submitted" value="1" /> 
									</td>
								</tr>
							
							</tbody>
					</table>
					</form>
				
				</td>
				
				<td width="30%" style="background:#ececec;padding:10px 5px;" valign="top">
					<h3>Get More Extensions</h3>
					
					<p>Vist <a href="http://exnetbd.com" target="_blank" title="Premium &amp; Free Extensions/Plugins for E-Commerce by ExNet, CV">ExNet</a> to get more free and premium extensions/plugins for your ecommerce platform.</p>
					
					<h3>Spreading the Word</h3>

					<ul style="list-style:dash">If you find this plugin helpful, you can:	
						<li>- Write and review about it in your blog</li>
						<li>- Share on your social media</li>
					</ul>
					
					<h3>Thank you for your support!</h3>
				</td>
				
			</tr>
			</table>
			
			</div>
			<br />
			
			<?php
		}

	function fbc_menu_items() {
		add_submenu_page( 'woocommerce' , __( 'FB Comment & Social Share', 'fbc' ), __( 'FBC Setting', 'fbc' ), 'manage_woocommerce', 'fbc-settings', 'fbc_options_page');
	}
	
	add_action( 'admin_menu', 'fbc_menu_items' );