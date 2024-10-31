<?php
/**
* Plugin Name: Rusty Top Bar
* Plugin URI: https://widgetmedia.co/wp/rusty-topbar/
* Description: A handy top bar for your websites messages.
* Version: 1.1
* Author: RustyBadRobot
* Author URI: https://widgetmedia.co/
*
*/

if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

if ( !class_exists( 'RustyTopbar' ) ) {

    class RustyTopbar {

			var $menu_id;

 	    public $capability;

 	    public function __construct() {

				load_plugin_textdomain('rusty-topbar', false, '/rusty-topbar/localization');

 		    add_action('admin_menu',                              array($this, 'add_admin_menu'));
 		    add_action('admin_enqueue_scripts',                   array($this, 'admin_enqueues'));

 		    add_action( 'wp_head',                                array($this, 'frontendHeader' ), 1000 );
 		    add_action( 'wp_footer',                              array($this, 'frontendFooter' ), 1000 );

 	    }

 	    /**
 	    * Register the management page
 	    *
 	    * @access public
 	    * @since 1.0
 	    */
 	    public function add_admin_menu() {
 	        $this->menu_id = add_management_page(__('Rusty Top Bar', 'rusty-topbar' ), __( 'Rusty Top Bar', 'rusty-topbar' ), 'manage_options', 'rusty-topbar', array($this, 'rusty_topbar_interface') );
 	    }

 	    public function admin_enqueues($hook_suffix) {

 	        if ($hook_suffix != $this->menu_id) {
 	            return;
 	        }

 	        wp_enqueue_style('rusty-custom-style', plugins_url('style.css', __FILE__), array(), '1.0');
 	        wp_enqueue_style( 'wp-color-picker' );
 	        wp_enqueue_script( 'cpa_custom_js', plugins_url( 'jquery.custom.js', __FILE__ ), array( 'jquery', 'wp-color-picker' ), '', true  );

 	    }

 	    function frontendHeader() {
 	        $this->output( 'rusty_header' );
 	    }

 	    function frontendFooter() {
 	        $this->output( 'rusty_footer' );
 	    }

 	    function output( $setting ) {

 	        // Ignore admin, feed, robots or trackbacks
 	        if ( is_admin() || is_feed() || is_robots() || is_trackback() ) {
 	            return;
 	        }

 	        $meta = get_option( 'rusty_topbar_active' );

 	        if ( trim( $meta ) != "active" ) {
 	            return;
 	        }

 	        //Render Footer
 	        if ( $setting == "rusty_footer" ) {

 	                if ( is_admin_bar_showing() ) {
 	                    $bar_top = "50px";
 	                } else {
 	                    $bar_top = "0";
 	                }

 	                $barBg = get_option( 'rusty_topbar_bg', '#E14938' );
 	                $barColor = get_option( 'rusty_topbar_color', '#ffffff' );
 	                $barScroll = get_option( 'rusty_topbar_scroll', 'fixed' );
 	                $barTxt = get_option( 'rusty_topbar_text' );

 	                echo '
 	                <script>
 	                var topBar = document.createElement("div");
 	                topBar.className = "rusty-banner";
 	                topBar.style.cssText = "position:' . esc_attr($barScroll) . ';top:' . esc_attr($bar_top) . ';left:0;width:100%;height:50px;z-index:9999;background-color:' . esc_attr($barBg) . ';";
 	                document.body.appendChild(topBar);
 	                var newSpan = document.createElement("span");
 	                newSpan.className = "rusty-holder";
 	                newSpan.style.cssText = "color:' . esc_attr($barColor) . ';";
 	                newSpan.innerHTML += "' . esc_attr($barTxt) . '";
 	                topBar.appendChild(newSpan);
 	                </script>';


 	        } elseif ( $setting == "rusty_header" ) {

 	            if ( is_admin_bar_showing() ) {
 	                $html_top = "82px";
 	            } else {
 	                $html_top = "50px";
 	            }

 	            echo '
 	            <style type="text/css">
 	            html { margin-top: ' . esc_attr($html_top) . ' !important; }
 	            .rusty-banner {
 	            display: table;
 	            table-layout: fixed;
 	            box-sizing: border-box;
 	            text-align: center;
 	            }
 	            .rusty-holder {
 	            display: table-cell;
 	            vertical-align: middle;
 	            font-size: 16px;
 	            }
 	            </style>';

 	        } else {

 	            return;


 	        }

 	    }





 	    /**
 	    * The user interface
 	    *
 	    * @access public
 	    * @since 1.0
 	    */
 	    public function rusty_topbar_interface() {

 		    global $wpdb;
 		    ?>

 		    <div id="message" class="updated fade" style="display:none"></div>

 		    <div class="wrap rusty-wrap">

 		        <?php

 		        // If the button was clicked
 		        if ( !empty($_POST['rusty-topbar'] ) ) {

 		            // Form nonce check
 		            check_admin_referer('rusty-topbar');

 		            if (!empty($_POST["rusty_topbar_settings"]["active"])) {

 		                $topActive = sanitize_text_field( $_POST["rusty_topbar_settings"]["active"] );

 		                update_option( 'rusty_topbar_active', $topActive, null );

 		            } else {

 		                update_option( 'rusty_topbar_active', 'false', null );

 		            }

 		            if (!empty($_POST["rusty_topbar_settings"]["scroll"])) {

 		                $topScroll = sanitize_text_field( $_POST["rusty_topbar_settings"]["scroll"] );

 		                update_option( 'rusty_topbar_scroll', $topScroll, null );

 		            } else {

 		                update_option( 'rusty_topbar_scroll', 'fixed', null );

 		            }

 		            if (!empty($_POST["rusty_topbar_settings"]["text"])) {

 		                $topTxt = sanitize_text_field( $_POST["rusty_topbar_settings"]["text"] );

 		                update_option( 'rusty_topbar_text', $topTxt, null );

 		            } else {

 		                update_option( 'rusty_topbar_text', '', null );

 		            }

 		            if (!empty($_POST["rusty_topbar_settings"]["background"])) {

 		                $topBg = sanitize_hex_color( $_POST["rusty_topbar_settings"]["background"] );

                        if ( $topBg ) {

 		                    update_option( 'rusty_topbar_bg', $topBg, null );
 		                }

 		            }

 		            if (!empty($_POST["rusty_topbar_settings"]["color"])) {

 		                $topColor = sanitize_hex_color( $_POST["rusty_topbar_settings"]["color"] );

                        if ( $topColor ) {

 		                    update_option( 'rusty_topbar_color', $topColor, null );
 		                }

 		            }


 		        }

 		        ?>

 		        <h1><?php _e('Rusty Top Bar', 'rusty-topbar'); ?></h1>

 		        <div class="card">

 		            <h2 class="title"><?php _e('Top Bar Options', 'rusty-topbar'); ?></h2>

 		            <p><span class="description"><?php printf( __( 'Update the style and text displayed on your top bar.', 'rusty-topbar' ) ); ?></span></p>

 		            <form action="<?php echo admin_url( 'tools.php?page=rusty-topbar' ); ?>" method="post">

 		                <?php wp_nonce_field('rusty-topbar') ?>

										<fieldset>

										    <?php $barActive = get_option( 'rusty_topbar_active' ); ?>

										    <?php $barTxt = get_option( 'rusty_topbar_text' ); ?>

											<?php $barBg = get_option( 'rusty_topbar_bg', '#E14938' ); ?>

											<?php $barColor = get_option( 'rusty_topbar_color', '#ffffff' ); ?>

											<?php $barScroll = get_option( 'rusty_topbar_scroll' ); ?>

											<fieldset>
											    <legend class="screen-reader-text"><span>Active</span></legend>
											    <input type="checkbox" id="active" name="rusty_topbar_settings[active]" value="active" <?php echo ( esc_attr($barActive) == "active" ? 'checked' : '' ); ?> >
											    <label for="active">Active</label>
											    </label>
											</fieldset>

											<fieldset>
											    <legend class="screen-reader-text"><span>Force Top Bar to scroll with the website.</span></legend>
											    <input type="checkbox" id="absolute" name="rusty_topbar_settings[scroll]" value="absolute" <?php echo ( esc_attr($barScroll) == "absolute" ? 'checked' : '' ); ?> >
											    <label for="absolute">Force Top Bar to scroll with the website.</label>
											</fieldset>

											<div class="textarea-wrap" id="description-wrap" style="margin-bottom: 12px;">
											    <label for="content">Content</label>
											    <textarea name="rusty_topbar_settings[text]" class="rusty-textarea-field large-text" class="mceEditor" rows="3" cols="15" autocomplete="off"><?php echo esc_textarea($barTxt); ?></textarea>
											 </div>

											<div class="input-text-wrap" id="title-wrap">
											    <input type="text" name="rusty_topbar_settings[background]" value="<?php echo esc_attr($barBg); ?>" data-default-color="#E14938" class="rusty-color-picker large-text" />
											    <label for="title">Top Bar Background</label>
											</div>

											<div class="input-text-wrap" id="title-wrap">
											    <input type="text" name="rusty_topbar_settings[color]" value="<?php echo esc_attr($barColor); ?>" data-default-color="#ffffff" class="rusty-color-picker large-text" />
											    <label for="title">Top Bar Text</label>
											 </div>

 		                </fieldset>

										<p class="submit">

											<input type="submit" name="rusty-topbar" id="rusty-topbar" class="button-primary" value="<?php _e( 'Save Changes', 'rusty-topbar' ); ?>" aria-label="Save Changes"/>

										</p>

 		            </form>

 		        </div>

		    </div>

 		    <?php
 	    }

		}

    //Start
    $RustyTopbar = new RustyTopbar();

}

?>
