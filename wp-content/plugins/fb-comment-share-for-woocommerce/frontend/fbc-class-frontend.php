<?php

if (!defined('ABSPATH'))
exit('<h1>Not Found</h1>The requested URL '.$_SERVER['SCRIPT_NAME'].' was not found on this server.'); //Exit if accessed directly

	if(!defined('FBCOMMENT')) return;
	
	add_filter( 'woocommerce_product_tabs', 'woocommerce_product_fbc_wc_tab' );
	add_action('woocommerce_before_single_product', 'fbc_wc_add_head');
	if( get_option('fbc_share_enabled') != 'yes'){
		if(get_option('fbc_share_post') == 'bottom'):
			add_action('woocommerce_single_product_summary', 'fbc_like_send', 8);
		else:
			add_action( 'woocommerce_product_thumbnails','fbc_like_send_thumb', 10 );
		endif;	
	}
	add_action('wp_head', 'enque_script');
	
	function woocommerce_product_fbc_wc_tab( $tabs ) {
					global $post, $product;
	  
					$fbc_wc_tab_options = array(
							'enabled' => get_post_meta($post->ID, 'fbc_wc_tab_enabled', true),
							'title' => get_post_meta($post->ID, 'fbc_wc_tab_title', true),
							'count' => get_post_meta($post->ID, 'fbc_wc_tab_count', true),
					);
					
					$url = get_permalink($post->ID);
					
					if(!$fbc_wc_tab_options['count']) 
						$ccount = '5';
						else
						$ccount = $fbc_wc_tab_options['count'];
									
					$fbc_scr = '<div class="fb-comments" data-href="'.$url.'" data-num-posts="'.$ccount.'" data-width="'.get_option('fbc_width').'" data-colorscheme="'.get_option('fbc_color_scheme').'" data-mobile="false"></div>';
					
	
					$commentCount = '('.fbc_comment_count().')';
					if($fbc_wc_tab_options['title']) $fbc_title = $fbc_wc_tab_options['title'];
						else $fbc_title = "Comment";  
	
							if ( $fbc_wc_tab_options['enabled'] != 'no' && get_option('fbc_enabled') != 'no' ){
									$tabs['fbc_wc_tabs'] = array(
										'title'   => $fbc_title .' '.$commentCount,
										'id' => 'test_multicheckbox',     
											'priority' => 35,
											'callback' => 'fbc_wc_tabs_panel_content',                               
											'content'  => $fbc_scr
									);
							}
	  
					return $tabs;
			}
	  
	function fbc_wc_tabs_panel_content( $key, $fbc_wc_tab_options ) {
					global $post, $product;
					
					$url = get_permalink($post->ID);
					
					$fbc_wc_tab_content = array(
							'enabled' => get_post_meta($post->ID, 'fbc_wc_tab_enabled', true),
							'title' => get_post_meta($post->ID, 'fbc_wc_tab_title', true),
							'count' => get_post_meta($post->ID, 'fbc_wc_tab_count', true),
					);
					
					if(!$fbc_wc_tab_content['count'])
						$ccount = '5';
						else
						$ccount = $fbc_wc_tab_content['count'];
						
					$fbcwc = '<div class="fb-comments" data-href="'.$url.'" data-num-posts="'.$ccount.'" data-width="'.get_option('fbc_width').'" data-colorscheme="'.get_option('fbc_color_scheme').'" data-mobile="false"></div>';
					
					$fbcoutput = do_shortcode( $fbcwc ) ;              
						
					echo '<h2 style="background:url('.FBC_LOCATION.'/assets/images/h2.png) no-repeat left top 10px; padding-left:20px;">' . $fbc_wc_tab_content['title'] . '</h2>';
					
					print $fbcoutput;
			}
	
	function fbc_wc_add_head () {
		global $post, $product;
			$fbc_wc_head = array(
				'enabled' => get_post_meta($post->ID, 'fbc_wc_tab_enabled', true),
				'title' => get_post_meta($post->ID, 'fbc_wc_tab_title', true),
				);
			if ( $fbc_wc_head['enabled'] != 'no' && get_option('fbc_enabled') != 'no'){
	
	?>
	
	<div id="fb-root"></div>  
	<script>(function(d, s, id) {  
	  var js, fjs = d.getElementsByTagName(s)[0];  
	  if (d.getElementById(id)) return;  
	  js = d.createElement(s); js.id = id;  
	  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=<?php echo get_option('fbc_app_id');?>";  
	  fjs.parentNode.insertBefore(js, fjs);  
	}(document, 'script', 'facebook-jssdk'));</script> 
	
	<?php
	
		}
	}
	
	function enque_script () {
		global $post, $product;
			$fbc_wc_header = array(
				'enabled' => get_post_meta($post->ID, 'fbc_wc_tab_enabled', true),
				'title' => get_post_meta($post->ID, 'fbc_wc_tab_title', true),
				);
			if ( $fbc_wc_header['enabled'] != 'no' && get_option('fbc_enabled') != 'no' ){	
			echo '
				<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
				';
			if( get_option('fbc_tw_enabled') != 'no'){ 	
			echo'
				<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
				';
			} if( get_option('fbc_gp_enabled') != 'no'){ 	
			echo'
				<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
				';
			} if( get_option('fbc_li_enabled') != 'no'){ 	
			echo'
				<script type="text/javascript" src="https://platform.linkedin.com/in.js"></script>
				';
			} if( get_option('fbc_pn_enabled') != 'no'){ 	
			echo'
				<script type="text/javascript" src="http://assets.pinterest.com/js/pinit.js"></script>
				';
			} echo '	
				<meta property="fb:app_id" content="'.get_option('fbc_app_id').'"/>
				';
		}
	}
	
	
	function fbc_comment_count() {  
		global $post, $product;  
	
		$url = get_permalink($post->ID);
		  
		$filecontent = file_get_contents('https://graph.facebook.com/?ids=' . $url);  
	
		$json = json_decode($filecontent);  
		$count = $json->$url->comments;  
		$wpCount = get_comments_number();  
		$realCount = $count + $wpCount;  
		if ($realCount == 0 || !isset($realCount)) {  
			$realCount = 0;  
		}  
		return $realCount;  
	}
	
	function fbc_like_send (){
		global $post, $product;
	
			$fbc_wc_like = array(
				'enabled' => get_post_meta($post->ID, 'fbc_wc_soc_share', true),
				'title' => get_post_meta($post->ID, 'fbc_wc_tab_title', true),
				);
			
			if ( $fbc_wc_like['enabled'] != 'no' && get_option('fbc_enabled') != 'no'):
				
			if( get_option('fbc_fb_enabled') != 'no'){
			?>
			<div class="fb-like" data-href="<?php echo the_permalink();?>" data-layout="standard" data-send="true" data-width="450" data-show-faces="false" data-action="like"></div>
			<?php } if( get_option('fbc_fbr_enabled') == 'yes'){ ?>
			<div class="fb-like" data-href="<?php echo the_permalink();?>" data-layout="standard" data-send="false" data-width="450" data-show-faces="false" data-action="recommend"></div>
			<?php } if( get_option('fbc_tw_enabled') != 'no'){ ?>
			<a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal" data-via="<?php echo get_option('fbc_twitter');?>" data-lang="en" data-related='wordpress' data-url="<?php echo the_permalink();?>" data-text="<?php echo single_post_title();?>"></a>
			<?php } if( get_option('fbc_gp_enabled') != 'no'){ ?>
			<g:plusone size="medium" annotation="bubble"></g:plusone>
			<?php } if( get_option('fbc_li_enabled') != 'no'){ ?>
			<script type="IN/Share" data-url="<?php echo the_permalink();?>" data-counter="right" data-showzero="right" data-onsuccess="<?php echo the_permalink();?>"></script>
			<?php } if( get_option('fbc_pn_enabled') != 'no'){ ?>
			<a href="//www.pinterest.com/pin/create/button/?url=<?php echo the_permalink();?>&media=<?php echo wp_get_attachment_thumb_url(get_post_thumbnail_id($post->ID)); ?>&description=<?php echo single_post_title();?>" data-pin-do="buttonPin" data-pin-config="beside"><img src="//assets.pinterest.com/images/pidgets/pin_it_button.png" /></a>
			<?php
			}
		endif;	
	}


	function fbc_like_send_thumb (){
		global $post, $product;
	
			$fbc_wc_like = array(
				'enabled' => get_post_meta($post->ID, 'fbc_wc_soc_share', true),
				'title' => get_post_meta($post->ID, 'fbc_wc_tab_title', true),
				);
			if ( $fbc_wc_like['enabled'] != 'no' && get_option('fbc_enabled') != 'no'):
				
			?>
			<div style="float:left; position:absolute; top:15px; left:21px; max-width:310px;" class="woocommerce-main-image"><div style="width:300px; background-color:#FFFFFF; opacity:0.90; filter:alpha(opacity=90); /* For IE8 and earlier */ border:1px dashed #CCCCCC;border-radius:3px; box-shadow: 1px 1px 1px #888888; padding:3px;">
			<?php	
			if( get_option('fbc_fb_enabled') != 'no'){
			?>
			<div class="fb-like" data-href="<?php echo the_permalink();?>" data-layout="standard" data-send="true" data-width="300" data-show-faces="false" data-action="like"></div>
			<?php } if( get_option('fbc_fbr_enabled') == 'yes'){ ?>
			<div class="fb-like" data-href="<?php echo the_permalink();?>" data-layout="standard" data-send="false" data-width="300" data-show-faces="false" data-action="recommend"></div>
			<?php } if( get_option('fbc_tw_enabled') != 'no'){ ?>
			<a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal" data-via="<?php echo get_option('fbc_twitter');?>" data-lang="en" data-related='wordpress' data-url="<?php echo the_permalink();?>" data-text="<?php echo single_post_title();?>"></a>
			<?php } if( get_option('fbc_gp_enabled') != 'no'){ ?>
			<g:plusone size="medium" annotation="bubble"></g:plusone>
			<?php } if( get_option('fbc_li_enabled') != 'no'){ ?>
			<script type="IN/Share" data-url="<?php echo the_permalink();?>" data-counter="right" data-showzero="right" data-onsuccess="<?php echo the_permalink();?>"></script>
			<?php } if( get_option('fbc_pn_enabled') != 'no'){ ?>
			<a href="//www.pinterest.com/pin/create/button/?url=<?php echo the_permalink();?>&media=<?php echo wp_get_attachment_thumb_url(get_post_thumbnail_id($post->ID)); ?>&description=<?php echo single_post_title();?>" data-pin-do="buttonPin" data-pin-config="beside"><img src="//assets.pinterest.com/images/pidgets/pin_it_button.png" style="border:none; width:60px;" /></a>
			<?php
			}
			echo "</div></div>";
		endif;
	}

