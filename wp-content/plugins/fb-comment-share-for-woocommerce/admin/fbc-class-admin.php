<?php

if (!defined('ABSPATH'))
exit('<h1>Not Found</h1>The requested URL '.$_SERVER['SCRIPT_NAME'].' was not found on this server.'); //Exit if accessed directly

	if(!defined('FBCOMMENT')) return;
	if(get_option('fbc_enabled') == 'yes'){
	add_action('woocommerce_product_write_panel_tabs', 'fbc_wc_tab');
	add_action('woocommerce_product_write_panels', 'fbc_wc_tab_options');
	add_action('woocommerce_process_product_meta', 'process_fbc_wc_tab', 10, 2);
	}
	
	function fbc_wc_tab() {
		echo '<li class="linked_product_tab linked_product_options"><a href="#fbc_wc_tab_option">'.__('FB Comment', 'woocommerce').'</a></li>';
	
	}
	
	function fbc_wc_tab_options() {
			global $post;
			
			$fbc_wc_tab_options = array(
					'title' => get_post_meta($post->ID, 'fbc_wc_tab_title', true),
					'count' => get_post_meta($post->ID, 'fbc_wc_tab_count', true),
			);
			
	?>
			<div id="fbc_wc_tab_option" class="panel woocommerce_options_panel">
				<div class="options_group">
					<p class="form-field">
						<?php woocommerce_wp_checkbox( array( 'id' => 'fbc_wc_tab_enabled', 'label' => __('Enable Facebook Comment Tab?', 'woocommerce'), 'description' => __('Enable this option to enable the facebook comment widget tab on the frontend.', 'woocommerce') ) ); ?>
					</p>
					<?php if( get_option('fbc_share_enabled') != 'yes'){ ?>
					<p class="form-field">
						<?php woocommerce_wp_checkbox( array( 'id' => 'fbc_wc_soc_share', 'label' => __('Enable Social Share?', 'woocommerce'), 'description' => __('Enable this option to enable the shocial share on the frontend.', 'woocommerce') ) ); ?>
					</p>
					<?php } ?>
				</div>
					
				<div class="options_group fbc_wc_tab_option">                                                                        
					<p class="form-field">
						<label for="fbc_wc_tab_title"><?php _e('FB Comment Title:', 'woocommerce'); ?></label>
						<input type="text" size="5" name="fbc_wc_tab_title" id="fbc_wc_tab_title" value="<?php echo @$fbc_wc_tab_options['title']; ?>" placeholder="<?php _e('Enter your FB Comment tab title', 'woocommerce'); ?>" />
				   </p>
					<p class="form-field">
						<label for="fbc_wc_tab_count"><?php _e('Comment Count:', 'woocommerce'); ?></label>
						<input type="text" size="3" name="fbc_wc_tab_count" id="fbc_wc_tab_count" value="<?php echo @$fbc_wc_tab_options['count']; ?>" placeholder="<?php _e('How much comment will be shown', 'woocommerce'); ?>" />
				   </p>
				</div>
			</div>
	<?php
	}
	
	function process_fbc_wc_tab( $post_id ) {
			update_post_meta( $post_id, 'fbc_wc_tab_enabled', ( isset($_POST['fbc_wc_tab_enabled']) && $_POST['fbc_wc_tab_enabled'] ) ? 'yes' : 'no' );
			update_post_meta( $post_id, 'fbc_wc_soc_share', ( isset($_POST['fbc_wc_soc_share']) && $_POST['fbc_wc_soc_share'] ) ? 'yes' : 'no' );
			update_post_meta( $post_id, 'fbc_wc_tab_title', $_POST['fbc_wc_tab_title']);
			update_post_meta( $post_id, 'fbc_wc_tab_count', $_POST['fbc_wc_tab_count']);
	}

