<?php 

    /*############  Admin Menu Class ################*/

class wpdevart_comment_admin_menu{
	
	private $menu_name;
	private $databese_parametrs;
	private $plugin_url;
	private $text_parametrs;

	/*###################### Constract params function ##################*/	
	
	function __construct($param){
		
		$this->menu_name=$param['menu_name'];
		$this->databese_parametrs=$param['databese_parametrs'];
		if(isset($params['plugin_url']))
			$this->plugin_url=$params['plugin_url'];
		else
			$this->plugin_url=trailingslashit(dirname(plugins_url('',__FILE__)));
			add_action( 'wp_ajax_wpdevart_comment_page_save', array($this,'save_in_databese') );
			add_action( 'add_meta_boxes', array($this,'wpdevart_comment_add_meta_box') );
			add_action( 'save_post', array($this,'wpdevar_save_post') );
	}

	/*###################### Meta Box function ##################*/	
	
	public function wpdevart_comment_add_meta_box() {

		$post_types = array( 'post', 'page' );

		foreach ( $post_types as $post_type ) {
	
			add_meta_box('myplugin_sectionid',	'Disable Wpdevart facebook comment',array($this,'generete_html_for_wpdevart_comment_box'),	$post_type	);
		}
	}
	
    /*############  HTML generating function  ################*/
	
	public function generete_html_for_wpdevart_comment_box($post){
		// Add field that we can check later.
		wp_nonce_field( 'wpdevar_save_post', 'wpdevart_facebook_meta_box_nonce' );
	
		/*
		 * Use get_post_meta() to retrieve the existing value
		 * From database, use the value for the form.
		 */
		$value = get_post_meta( $post->ID, '_disabel_wpdevart_facebook_comment', true );
		echo '<label for="wpdevart_disable_field">';
		echo  'Wpdevart Facebook comment &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		echo '</label> ';
		echo '<select id="wpdevart_disable_field" name="wpdevart_disable_field"><option value="enable">Enable</option><option '.(($value=='disable')?'selected="selected"':'').' value="disable">Disable</option></select>';
	}
	
	/*###################### Post save function ##################*/	
	
	function wpdevar_save_post( $post_id ) {

		
		if ( ! isset( $_POST['wpdevart_facebook_meta_box_nonce'] ) ) {	return;	}
		
		if ( ! wp_verify_nonce( $_POST['wpdevart_facebook_meta_box_nonce'], 'wpdevar_save_post' ) ) {	return;	}
		
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {	return;	}
	
		
		if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}
		if ( ! isset( $_POST['wpdevart_disable_field'] ) ) {
			return;
		}
	
		$my_data = sanitize_text_field( $_POST['wpdevart_disable_field'] );
	
		
		update_post_meta( $post_id, '_disabel_wpdevart_facebook_comment', $my_data );
	}

	/*############################ Posts/Pages insert button shortcode part ###################################*/

	public function create_menu(){
		$main_page 	 	  = add_menu_page( $this->menu_name, $this->menu_name, 'manage_options', str_replace( ' ', '-', $this->menu_name), array($this, 'main_menu_function'),$this->plugin_url.'images/facebook_menu_icon.png');
		$page_wpdevart_comment	  =	add_submenu_page($this->menu_name,  $this->menu_name,  $this->menu_name, 'manage_options', str_replace( ' ', '-', $this->menu_name), array($this, 'main_menu_function'));
		$page_wpdevart_comment	  = add_submenu_page( str_replace( ' ', '-', $this->menu_name), 'Featured Plugins', 'Featured Plugins', 'manage_options', 'wpdevart-comment-featured-plugins', array($this, 'featured_plugins'));
		add_action('admin_print_styles-' .$main_page, array($this,'menu_requeried_scripts'));
		add_action('admin_print_styles-' .$page_wpdevart_comment, array($this,'menu_requeried_scripts'));		
	}

	/*###################### Requaried scripts function ##################*/	
	
	public function menu_requeried_scripts(){
		wp_enqueue_script('wp-color-picker');		
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'comment-box-admin-script' ); 
		wp_enqueue_style('comment-box-admin-style');
	}
	
	/*###################### Generate parameters function ##################*/		
	
	private function generete_parametrs($page_name){
		$page_parametrs=array();
		if(isset($this->databese_parametrs[$page_name])){
			foreach($this->databese_parametrs[$page_name] as $key => $value){
				$page_parametrs[$key]=get_option($key,$value);
			}
			return $page_parametrs;
		}
		return NULL;
		
	}
	
	/*###################### Database function ##################*/	
	
	public function save_in_databese(){
		$kk=1;	
		if(isset($_POST['wpdevart_comment_options_nonce']) && wp_verify_nonce( $_POST['wpdevart_comment_options_nonce'],'wpdevart_comment_options_nonce')){
			foreach($this->databese_parametrs[$_POST['curent_page']] as $key => $value){
				if(isset($_POST[$key]))
					update_option($key,$_POST[$key]);
				else{
					$kk=0;
					printf($this->text_parametrs['error_in_saving'],$key);
				}
			}	
		}
		else{		
			die($this->text_parametrs['authorize_problem']);
		}
		if($kk==0){
			exit;
		}
		die($this->text_parametrs['parametrs_sucsses_saved']);
	}
	
	/*###################### Main menu function ##################*/		
	
	public function main_menu_function(){	
	
	$enable_disable=$this->generete_parametrs('general_save_parametr');	
	$enable_disable=$enable_disable['wpdevart_comment_page_mode'];
		?>
        <script>
        var wpdevart_comment_ajaxurl="<?php echo admin_url( 'admin-ajax.php'); ?>";
		var wpdevart_comment_plugin_url="<?php echo $this->plugin_url; ?>";
		var wpdevart_comment_parametrs_sucsses_saved="<?php echo $this->text_parametrs['parametrs_sucsses_saved'] ?>";
		var wpdevart_comment_all_parametrs = <?php echo json_encode($this->databese_parametrs); ?>;
        </script>
      <div class="coming_title"><h1>Facebook Comments <a style="text-decoration:none;" href="http://wpdevart.com/wordpress-facebook-comments-plugin/"><span style="color: rgba(10, 154, 62, 1);"> (Upgrade to Pro Version)</span></a><a target="blank" href="<?php echo wpdevart_comment_support_url; ?>" class="wp_support">Support</a></h1></div>      
      
	<br>
     
        <div class="wp-table right_margin">
        
        
            <div class="left_sections">
				<?php
                $this->generete_wpdevart_main_section($this->generete_parametrs('wpdevart_comments_box'));	
                ?>
            </div>
            <div class="right_sections">
                <div class="main_parametrs_group_div">
                    <div class="head_panel_div">                    
                    	<span class="title_parametrs_group">Facebook Comments plugin user manual</span>       
                    </div>
                    <div class="inside_information_div">
                        <table class="wp-list-table widefat fixed posts section_parametrs_table">                            
                            <tbody> 
                                <tr>
                                    <td>
                                        <div class="pea_admin_box">
                                        
                                            <p>Here's the short user manual that should help you to insert Facebook Comments Box into your website.</p>
                                           <p style="font-weight:bolder"><span style="color:red">APP ID</span> - you can create your App Id on this page - <a style="color:#0073aa" target="_blank" href="https://developers.facebook.com/apps">https://developers.facebook.com/apps.</a>
Also, here is another tutorial(from other source) of creating App Id, you can check it - <a style="color:#0073aa" target="_blank" href="https://help.yahoo.com/kb/SLN18861.html">https://help.yahoo.com/kb/SLN18861.html</a>.</p>
                                            <p>If you select the option "Display comments on" - Home, Post, Page, then the Facebook Comments box will be added on every page/post of your website. </p> 
                                            <p>Also, you can insert the Facebook Comments box manually in any page/post or even in Php code using plugin shortcode. You can disable comments on single pages or posts as well by disabling it(find the otion below posts/pages).</p>
                                            
                                            <p><strong>Here's an example of using the Facebook comments shortcode in posts, pages:</strong></p>
                                            <p><code>[wpdevart_facebook_comment curent_url="http://developers.facebook.com/docs/plugins/comments/" title_text="Facebook Comment" order_type="social" title_text_color="#000000" title_text_font_size="22" title_text_font_famely="monospace" title_text_position="left" width="100%" bg_color="#d4d4d4" animation_effect="random"  count_of_comments="2" ]</code></p>
                                            
                                            <p><strong>Here is an example of using the Facebook comments box shortcode in PHP code:</strong></p>
                                            <p><code>&lt;?php echo do_shortcode('[wpdevart_facebook_comment curent_url="http://developers.facebook.com/docs/plugins/comments/" order_type="social" title_text="Facebook Comment" title_text_color="#000000" title_text_font_size="22" title_text_font_famely="monospace" title_text_position="left" width="100%" bg_color="#d4d4d4" animation_effect="random"  count_of_comments="3" ]'); ?&gt;</code></p>
                                            
                                            <p><strong>Here are explanation of Facebook comments shortcode attributes.</strong></p>
                                            
                                            <p><strong>Curent_url</strong> - Type the page URL from where you need to show Facebook comments </p>
                                            <p><strong>Title_text</strong> - Type here Facebook comments box title</p>
                                            <p><strong>Colorscheme</strong> <span class="pro_feature"> (pro)</span> - Select Facebook comments box color scheme.Can be "light" or "dark".</p>
                                            <p><strong>Order_type</strong> - Choose Facebook comments box order type.The order to use when displaying comments. Can be "social", "reverse_time", or "time". </p>
                                            <p><strong>Title_text_color</strong> - Select Facebook comments box title text color</p>
                                            <p><strong>Title_text_font_size</strong> - Type Facebook comments box title font-size(px)</p>
                                            <p><strong>Title_text_font_famely</strong> - Select Facebook comments box title font family</p>
                                            <p><strong>Title_text_position</strong> - Select Facebook comments box title position</p>
                                            <p><strong>Width</strong> - Type here the Facebook comments box width(px)</p>
                                            <p><strong>Count_of_comments</strong> - Type here the number of Facebook comments you need to display</p>
                                            <p><strong>Bg_color</strong> <span class="pro_feature"> (pro)</span> - Select Facebook comments background color</p>
                                            <p><strong>Animation_effect</strong> <span class="pro_feature"> (pro)</span> - Select the animation effect for Facebook comments box</p>

                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>            
                </div> 
            </div>
        </div>       
       <?php
	  wp_nonce_field('wpdevart_comment_options_nonce','wpdevart_comment_options_nonce');
	}
	
	
	/*#########################  Facebook Comments Box main settings #################################*/
	
	public function generete_wpdevart_main_section($page_parametrs){

		?>
		<div class="main_parametrs_group_div " >
			<div class="head_panel_div">
				<span class="title_parametrs_group">Comment box settings</span>
				<span class="enabled_or_disabled_parametr"></span>
				<span class="open_or_closed"></span>         
			</div>
			<div class="inside_information_div">
				<table class="wp-list-table widefat fixed posts section_parametrs_table">                            
				<tbody> 
                
                
                 	<tr>
						<td>
							APP ID <span style="color:red">Important</span>  <span title="Type here your Facebook App ID" class="desription_class">?</span>
						</td>
						<td>
							<input type="text" name="wpdevart_comment_facebook_app_id"   id="wpdevart_comment_facebook_app_id" value="<?php echo $page_parametrs['wpdevart_comment_facebook_app_id'] ?>">
						</td>                
					</tr>
               		<tr>
						<td>
							 Title <span title="Type here Facebook comments box title" class="desription_class">?</span>
						</td>
						<td>
							<input type="text" name="wpdevart_comment_title_text" id="wpdevart_comment_title_text" value="<?php echo $page_parametrs['wpdevart_comment_title_text'] ?>">
						</td>                
					</tr>
                    <tr >
                        <td>
                           Color scheme <span class="pro_feature"> (pro)</span> <span title="Select Comments box color scheme" class="desription_class">?</span>
                        </td>
                        <td>
 							<select class="pro_select" id="wpdevart_comments_box_color_scheme">
                            	<option value="light" selected="selected">Light</option>
                                <option value="dark">Dark</option>
                            </select>                       
                        </td>                
                    </tr>
                     <tr >
                        <td>
                           Order Type <span title="Choose comments order type" class="desription_class">?</span>
                        </td>
                        <td>
 							<select id="wpdevart_comments_box_order_type">
                            	<option <?php selected($page_parametrs['wpdevart_comments_box_order_type'],'light') ?> value="social" >Social</option>
                                <option <?php selected($page_parametrs['wpdevart_comments_box_order_type'],'reverse_time') ?> value="reverse_time">Newest</option>
                                <option <?php selected($page_parametrs['wpdevart_comments_box_order_type'],'time') ?> value="time">Oldest</option>
                            </select>                       
                        </td>                
                    </tr>
                     <tr >
                        <td>
                           Title text color <span title="Select Facebook comments box title text color" class="desription_class">?</span>
                        </td>
                        <td>
                            <input type="text" class="color_option" id="wpdevart_comment_title_text_color" name="wpdevart_comment_title_text_color"  value="<?php echo $page_parametrs['wpdevart_comment_title_text_color'] ?>"/>
                         </td>                
                    </tr>
                    <tr>
						<td>
							Title font-size <span title="Type Facebook comments box title font-size(px)" class="desription_class">?</span>
						</td>
						<td>
							<input type="text" name="wpdevart_comment_title_text_font_size" id="wpdevart_comment_title_text_font_size" value="<?php echo $page_parametrs['wpdevart_comment_title_text_font_size'] ?>">Px
						</td>                
					</tr>
                    <tr>
						<td>
							Title font family <span title=" Select Facebook comments title font family" class="desription_class">?</span>
						</td>
						<td>
							<?php $this->create_select_element_for_font('wpdevart_comment_title_text_font_famely',$page_parametrs['wpdevart_comment_title_text_font_famely']) ?>
						</td>                
					</tr>
                    <tr >
                        <td>
                           Title position <span title="Select Facebook comments title position" class="desription_class">?</span>
                        </td>
                        <td>
                            <select id="wpdevart_comment_title_text_position">
                            	<option value="left" <?php selected($page_parametrs['wpdevart_comment_title_text_position'],'left') ?>>Left</option>
                                <option value="center" <?php selected($page_parametrs['wpdevart_comment_title_text_position'],'center') ?>>Center</option>
                                <option value="right" <?php selected($page_parametrs['wpdevart_comment_title_text_position'],'right') ?>>Right</option>
                            </select>
                         </td>                
                    </tr>
                	<tr>
						<td>
							Display comments on<span title="Select where to display Facebook comments" class="desription_class">?</span>
						</td>
						<td>
                        <?php $jsone_wpdevart_comments_box_show_in= json_decode(stripslashes($page_parametrs['wpdevart_comments_box_show_in']), true);?>  
                        	<input id="wpdevart_comment_show_in_home" type="checkbox" value="home" class="" size="" <?php checked($jsone_wpdevart_comments_box_show_in['home'],true) ?>><small>Home</small><br>                              
                            <input id="wpdevart_comment_show_in_post" type="checkbox" value="post" class="" size="" <?php checked($jsone_wpdevart_comments_box_show_in['post'],true) ?>><small>Post</small><br>
                            <input id="wpdevart_comment_show_in_page" type="checkbox" value="page" class="" size="" <?php checked($jsone_wpdevart_comments_box_show_in['page'],true) ?>><small>Page</small><br>
                            <input type="hidden" id="wpdevart_comments_box_show_in" class="wpdevart_comment_hidden_parametr" value='<?php echo stripslashes($page_parametrs['wpdevart_comments_box_show_in']); ?>'>
                           
                        </td>                
					</tr> 
                    <tr >
                        <td>
                           Position <span class="pro_feature"> (pro)</span>  <span title="Select Facebook comments box position(before or after WordPress standard comments)" class="desription_class">?</span>
                        </td>
                        <td>
                             <select class="pro_select" id="wpdevart_comments_box_vertical_position">
                            	<option value="bottom" selected="selected">Bottom</option>
                                <option value="top">Top</option>
                            </select>
                         </td>                
                    </tr>
                    
                    <tr>
						<td>
							Comments box width <span title="Type here the Facebook comments box width(px)" class="desription_class">?</span>
						</td>
						<td>
							<input type="text" name="wpdevart_comments_box_width" id="wpdevart_comments_box_width" value="<?php echo $page_parametrs['wpdevart_comments_box_width'] ?>">
						</td>                
					</tr>
                     <tr>
						<td>
							Number of comments <span title="Type here the number of Facebook comments to show" class="desription_class">?</span>
						</td>
						<td>
							<input type="text" name="wpdevart_comments_box_count_of_comments" id="wpdevart_comments_box_count_of_comments" value="<?php echo $page_parametrs['wpdevart_comments_box_count_of_comments'] ?>">
						</td>                
					</tr>
                    <tr >
                        <td>
                           Background color <span class="pro_feature"> (pro)</span> <span title=" Select Facebook comments background color" class="desription_class">?</span>
                        </td>
                        <td>
                           <div class="disabled_picker">
                                <div class="wp-picker-container"><a tabindex="0" class="wp-color-result" title="Select Color" data-current="Current Color" style="background-color: rgb(255, 255, 255);"></a></div>
                            </div>
                         </td>                
                    </tr>  
                	 <tr>
						<td>
							Animation effect <span class="pro_feature"> (pro)</span>  <span title="Select the animation effect for Facebook comments box" class="desription_class">?</span>
						</td>
						<td>
							<?php  wpdevart_comment_setting::generete_animation_select('animation_effect','none') ?>
						</td>                
					</tr>
                
                     <tr>
						<td>
							Default language <span title="Type here Facebook comments default language code(en_US,de_DE...)" class="desription_class">?</span>
						</td>
						<td>
							<input type="text" name="wpdevart_comments_box_locale"   id="wpdevart_comments_box_locale" value="<?php echo $page_parametrs['wpdevart_comments_box_locale'] ?>">(en_US,de_DE...)
						</td>                
					</tr>
                   
				</tbody>
					<tfoot>
						<tr>
							<th colspan="2" width="100%"><button type="button" id="wpdevart_comments_box" class="save_section_parametrs button button-primary"><span class="save_button_span">Save Section</span> <span class="saving_in_progress"> </span><span class="sucsses_save"> </span><span class="error_in_saving"> </span></button><span class="error_massage"> </span></th>
						</tr>
					</tfoot>       
				</table>
			</div>     
		</div>        
		<?php	
	}
	
	/*###################### Featured plugin function ##################*/	
	
	public function featured_plugins(){
		$plugins_array=array(
			'gallery_album'=>array(
						'image_url'		=>	$this->plugin_url.'images/featured_plugins/gallery-album-icon.png',
						'site_url'		=>	'http://wpdevart.com/wordpress-gallery-plugin',
						'title'			=>	'WordPress Gallery plugin',
						'description'	=>	'Gallery plugin is an useful tool that will help you to create Galleries and Albums. Try our nice Gallery views and awesome animations.'
						),		
			'coming_soon'=>array(
						'image_url'		=>	$this->plugin_url.'images/featured_plugins/coming_soon.jpg',
						'site_url'		=>	'http://wpdevart.com/wordpress-coming-soon-plugin/',
						'title'			=>	'Coming soon and Maintenance mode',
						'description'	=>	'Coming soon and Maintenance mode plugin is an awesome tool to show your visitors that you are working on your website to make it better.'
						),
			'Contact forms'=>array(
						'image_url'		=>	$this->plugin_url.'images/featured_plugins/contact_forms.png',
						'site_url'		=>	'http://wpdevart.com/wordpress-contact-form-plugin/',
						'title'			=>	'Contact Form Builder',
						'description'	=>	'Contact Form Builder plugin is an handy tool for creating different types of contact forms on your WordPress websites.'
						),	
			'Booking Calendar'=>array(
						'image_url'		=>	$this->plugin_url.'images/featured_plugins/Booking_calendar_featured.png',
						'site_url'		=>	'http://wpdevart.com/wordpress-booking-calendar-plugin/',
						'title'			=>	'WordPress Booking Calendar',
						'description'	=>	'WordPress Booking Calendar plugin is an awesome tool to create a booking system for your website. Create booking calendars in a few minutes.'
						),	
			'youtube'=>array(
						'image_url'		=>	$this->plugin_url.'images/featured_plugins/youtube.png',
						'site_url'		=>	'http://wpdevart.com/wordpress-youtube-embed-plugin',
						'title'			=>	'WordPress YouTube Embed',
						'description'	=>	'YouTube Embed plugin is an convenient tool for adding video to your website. Use YouTube Embed plugin to add YouTube videos in posts/pages, widgets.'
						),
			'countdown'=>array(
						'image_url'		=>	$this->plugin_url.'images/featured_plugins/countdown.jpg',
						'site_url'		=>	'http://wpdevart.com/wordpress-countdown-plugin/',
						'title'			=>	'WordPress Countdown plugin',
						'description'	=>	'WordPress Countdown plugin is an nice tool to create and insert countdown timers into your posts/pages and widgets.'
						),
			'lightbox'=>array(
						'image_url'		=>	$this->plugin_url.'images/featured_plugins/lightbox.png',
						'site_url'		=>	'http://wpdevart.com/wordpress-lightbox-plugin',
						'title'			=>	'WordPress Lightbox plugin',
						'description'	=>	'WordPress lightbox plugin is an high customizable and responsive product for displaying images and videos in popup.'
						),
			'facebook'=>array(
						'image_url'		=>	$this->plugin_url.'images/featured_plugins/facebook.jpg',
						'site_url'		=>	'http://wpdevart.com/wordpress-facebook-like-box-plugin',
						'title'			=>	'Facebook Like Box',
						'description'	=>	'Our Facebook like box plugin will help you to display Facebook like box on your wesite, just add Facebook Like box widget to your sidebar and use it..'
						),
			'poll'=>array(
						'image_url'		=>	$this->plugin_url.'images/featured_plugins/poll.png',
						'site_url'		=>	'http://wpdevart.com/wordpress-polls-plugin',
						'title'			=>	'WordPress Poll plugin',
						'description'	=>	'WordPress Polls plugin is an wonderful tool for creating polls and survey forms for your visitors. You can use our polls on widgets, posts and pages.'
						),	
            'facebook-comments'=>array(
						'image_url'		=>	$this->plugin_url.'images/featured_plugins/facebook-comments-icon.png',
						'site_url'		=>	'http://wpdevart.com/wordpress-facebook-comments-plugin/',
						'title'			=>	'WordPress Facebook comments',
						'description'	=>	'Our Facebook comments plugin will help you to display Facebook Comments on your website. You can use Facebook Comments on your pages/posts.'
						),
			'scroll'=>array(
						'image_url'		=>	$this->plugin_url.'images/featured_plugins/Scroll.png',
						'site_url'		=>	'https://wordpress.org/plugins/wp-scroll-2',
						'title'			=>	'Scroll To Top',
						'description'	=>	'Scroll to top plugin is an simple and nice plugin with the standard scroll settings. You can use it on your website different sides.'
						),						
			
		);
		?>
        <style>
         .featured_plugin_main{
			 background-color: #ffffff;
			 border: 1px solid #dedede;
			 box-sizing: border-box;
			 float:left;
			 margin-right:20px;
			 margin-bottom:20px;
			 
			 width:450px;
		 }
		.featured_plugin_image{
			padding: 15px;
			display: inline-block;
			float:left;
		}
		.featured_plugin_image a{
		  display: inline-block;
		}
		.featured_plugin_information{			
			float: left;
			width: auto;
			max-width: 282px;

		}
		.featured_plugin_title{
			color: #0073aa;
			font-size: 18px;
			display: inline-block;
		}
		.featured_plugin_title a{
			text-decoration:none;
					
		}
		.featured_plugin_title h4{
			margin:0px;
			margin-top: 20px;
			margin-bottom:8px;			  
		}
		.featured_plugin_description{
			display: inline-block;
		}
        
        </style>
        <script>
		
        jQuery(window).resize(wpdevart_comment_feature_resize);
		jQuery(document).ready(function(e) {
            wpdevart_comment_feature_resize();
        });
		
		function wpdevart_comment_feature_resize(){
			var wpdevart_comment_width=jQuery('.featured_plugin_main').eq(0).parent().width();
			var count_of_elements=Math.max(parseInt(wpdevart_comment_width/450),1);
			var width_of_plugin=((wpdevart_comment_width-count_of_elements*24-2)/count_of_elements);
			jQuery('.featured_plugin_main').width(width_of_plugin);
			jQuery('.featured_plugin_information').css('max-width',(width_of_plugin-160)+'px');
		}
       	</script>
        	<h2>Featured Plugins</h2>
            <br>
            <br>
            <?php foreach($plugins_array as $key=>$plugin) { ?>
            <div class="featured_plugin_main">
            	<span class="featured_plugin_image"><a target="_blank" href="<?php echo $plugin['site_url'] ?>"><img src="<?php echo $plugin['image_url'] ?>"></a></span>
                <span class="featured_plugin_information">
                	<span class="featured_plugin_title"><h4><a target="_blank" href="<?php echo $plugin['site_url'] ?>"><?php echo $plugin['title'] ?></a></h4></span>
                    <span class="featured_plugin_description"><?php echo $plugin['description'] ?></span>
                </span>
                <div style="clear:both"></div>                
            </div>
            <?php } 
	}
	
	/*######################################### Fonts Function #######################################*/

	private function create_select_element_for_font($select_id='',$curent_font='none'){
	?>
   <select id="<?php echo $select_id; ?>" name="<?php echo $select_id; ?>">
   
        <option <?php selected('Arial,Helvetica Neue,Helvetica,sans-serif',$curent_font); ?> value="Arial,Helvetica Neue,Helvetica,sans-serif">Arial *</option>
        <option <?php selected('Arial Black,Arial Bold,Arial,sans-serif',$curent_font); ?> value="Arial Black,Arial Bold,Arial,sans-serif">Arial Black *</option>
        <option <?php selected('Arial Narrow,Arial,Helvetica Neue,Helvetica,sans-serif',$curent_font); ?> value="Arial Narrow,Arial,Helvetica Neue,Helvetica,sans-serif">Arial Narrow *</option>
        <option <?php selected('Courier,Verdana,sans-serif',$curent_font); ?> value="Courier,Verdana,sans-serif">Courier *</option>
        <option <?php selected('Georgia,Times New Roman,Times,serif',$curent_font); ?> value="Georgia,Times New Roman,Times,serif">Georgia *</option>
        <option <?php selected('Times New Roman,Times,Georgia,serif',$curent_font); ?> value="Times New Roman,Times,Georgia,serif">Times New Roman *</option>
        <option <?php selected('Trebuchet MS,Lucida Grande,Lucida Sans Unicode,Lucida Sans,Arial,sans-serif',$curent_font); ?> value="Trebuchet MS,Lucida Grande,Lucida Sans Unicode,Lucida Sans,Arial,sans-serif">Trebuchet MS *</option>
        <option <?php selected('Verdana,sans-serif',$curent_font); ?> value="Verdana,sans-serif">Verdana *</option>
        <option <?php selected('American Typewriter,Georgia,serif',$curent_font); ?> value="American Typewriter,Georgia,serif">American Typewriter</option>
        <option <?php selected('Andale Mono,Consolas,Monaco,Courier,Courier New,Verdana,sans-serif',$curent_font); ?> value="Andale Mono,Consolas,Monaco,Courier,Courier New,Verdana,sans-serif">Andale Mono</option>
        <option <?php selected('Baskerville,Times New Roman,Times,serif',$curent_font); ?> value="Baskerville,Times New Roman,Times,serif">Baskerville</option>
        <option <?php selected('Bookman Old Style,Georgia,Times New Roman,Times,serif',$curent_font); ?> value="Bookman Old Style,Georgia,Times New Roman,Times,serif">Bookman Old Style</option>
        <option <?php selected('Calibri,Helvetica Neue,Helvetica,Arial,Verdana,sans-serif',$curent_font); ?> value="Calibri,Helvetica Neue,Helvetica,Arial,Verdana,sans-serif">Calibri</option>
        <option <?php selected('Cambria,Georgia,Times New Roman,Times,serif',$curent_font); ?> value="Cambria,Georgia,Times New Roman,Times,serif">Cambria</option>
        <option <?php selected('Candara,Verdana,sans-serif',$curent_font); ?> value="Candara,Verdana,sans-serif">Candara</option>
        <option <?php selected('Century Gothic,Apple Gothic,Verdana,sans-serif',$curent_font); ?> value="Century Gothic,Apple Gothic,Verdana,sans-serif">Century Gothic</option>
        <option <?php selected('Century Schoolbook,Georgia,Times New Roman,Times,serif',$curent_font); ?> value="Century Schoolbook,Georgia,Times New Roman,Times,serif">Century Schoolbook</option>
        <option <?php selected('Consolas,Andale Mono,Monaco,Courier,Courier New,Verdana,sans-serif',$curent_font); ?> value="Consolas,Andale Mono,Monaco,Courier,Courier New,Verdana,sans-serif">Consolas</option>
        <option <?php selected('Constantia,Georgia,Times New Roman,Times,serif',$curent_font); ?> value="Constantia,Georgia,Times New Roman,Times,serif">Constantia</option>
        <option <?php selected('Corbel,Lucida Grande,Lucida Sans Unicode,Arial,sans-serif',$curent_font); ?> value="Corbel,Lucida Grande,Lucida Sans Unicode,Arial,sans-serif">Corbel</option>
        <option <?php selected('Franklin Gothic Medium,Arial,sans-serif',$curent_font); ?> value="Franklin Gothic Medium,Arial,sans-serif">Franklin Gothic Medium</option>
        <option <?php selected('Garamond,Hoefler Text,Times New Roman,Times,serif',$curent_font); ?> value="Garamond,Hoefler Text,Times New Roman,Times,serif">Garamond</option>
        <option <?php selected('Gill Sans MT,Gill Sans,Calibri,Trebuchet MS,sans-serif',$curent_font); ?> value="Gill Sans MT,Gill Sans,Calibri,Trebuchet MS,sans-serif">Gill Sans MT</option>
        <option <?php selected('Helvetica Neue,Helvetica,Arial,sans-serif',$curent_font); ?> value="Helvetica Neue,Helvetica,Arial,sans-serif">Helvetica Neue</option>
        <option <?php selected('Hoefler Text,Garamond,Times New Roman,Times,sans-serif',$curent_font); ?> value="Hoefler Text,Garamond,Times New Roman,Times,sans-serif">Hoefler Text</option>
        <option <?php selected('Lucida Bright,Cambria,Georgia,Times New Roman,Times,serif',$curent_font); ?> value="Lucida Bright,Cambria,Georgia,Times New Roman,Times,serif">Lucida Bright</option>
        <option <?php selected('Lucida Grande,Lucida Sans,Lucida Sans Unicode,sans-serif',$curent_font); ?> value="Lucida Grande,Lucida Sans,Lucida Sans Unicode,sans-serif">Lucida Grande</option>
        <option <?php selected('monospace',$curent_font); ?> value="monospace">monospace</option>
        <option <?php selected('Palatino Linotype,Palatino,Georgia,Times New Roman,Times,serif',$curent_font); ?> value="Palatino Linotype,Palatino,Georgia,Times New Roman,Times,serif">Palatino Linotype</option>
        <option <?php selected('Tahoma,Geneva,Verdana,sans-serif',$curent_font); ?> value="Tahoma,Geneva,Verdana,sans-serif">Tahoma</option>
        <option <?php selected('Rockwell, Arial Black, Arial Bold, Arial, sans-serif',$curent_font); ?> value="Rockwell, Arial Black, Arial Bold, Arial, sans-serif">Rockwell</option>
    </select>
    <?php
	}
	
}