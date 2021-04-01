<?php
/**
 * Plugin Name: TBC Swiper Integration
 * Description: Integrates SwiperJS
 * Version: 1.0.0
 * Author: Russ Voisan
 *
 * @package tbc-swiperjs
 */


//Load the swiper js and css on pages with our meta key
add_action('wp_enqueue_scripts', 'tbc_load_swiper');
function tbc_load_swiper(){
    if(is_page()){ //Check if we are viewing a page
 	      global $wp_query;
        //Get Option from post meta data
        $swiper_option = get_post_meta( $wp_query->post->ID, '_swiper_meta_key', true );
        if($swiper_option == 'open'){
            //If swiper option is open, load the js and css
            wp_enqueue_script('swiperjs', plugin_dir_url( __FILE__ ) . 'src/node_modules/swiper/swiper-bundle.js');
            wp_enqueue_style('swipercss', plugin_dir_url( __FILE__ ) . 'src/node_modules/swiper/swiper-bundle.css');
 	     }
    }
 }

//Create a custom meta box in the editor for toggling swiper assets on a page
add_action( 'add_meta_boxes', 'tbc_swiper_add_custom_box' );
function tbc_swiper_add_custom_box() {
    $screens = [ 'page' ];
    foreach ( $screens as $screen ) {
        add_meta_box(
            'tbc_swiperjs_box_id1',                 // Unique ID
            'TBC Swiper',                           // Box title
            'tbc_swiper_custom_box_html',           // Content callback, must be of type callable
            $screen,                                // Which editors this box shows up in
            'side'                                  // Location in editor
        );
    }
}

//Html for meta box in editor
function tbc_swiper_custom_box_html( $post ) {
  $swiper_value = get_post_meta( $post->ID, '_swiper_meta_key', true );
  ?>
  <p class="meta-options">
	   <label for="swiper_status" class="selectit"><input name="swiper_status" type="checkbox" id="swiper_status" value="open" <?php checked( $swiper_value, 'open' ); ?> /> <?php _e( 'Enable SwiperJS' ); ?></label><br />
  </p>
	<?php
}

//Save our custom field value
add_action( 'save_post', 'tbc_swiper_save_postdata' );
function tbc_swiper_save_postdata( $post_id ) {
    update_post_meta(
        $post_id,
        '_swiper_meta_key',
        $_POST['swiper_status']
    );
}

//Prevent saving meta box order
add_action('check_ajax_referer', 'prevent_meta_box_order');
function prevent_meta_box_order( $action ) {
   if ('meta-box-order' == $action ) {
      die('-1');
   }
}

/*
//print post meta at the top for debugging
add_action('wp_head', 'output_all_postmeta' );
function output_all_postmeta() {

	$postmetas = get_post_meta(get_the_ID());

	foreach($postmetas as $meta_key=>$meta_value) {
		echo $meta_key . ' : ' . $meta_value[0] . '<br/>';
	}
}
*/
