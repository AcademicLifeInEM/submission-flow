<?php

/*
 *	Plugin Name: ALiEM Submission Flow
 *	Plugin URI: null
 *	Description: Internal plugin to enhance new submission flow
 *	Version: 0.1.0
 *	Author: Derek P Sifford
 *	Author URI: http://www.twitter.com/flightmed1
 *	License: GPL3
 *	License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/

// Enqueue Javascript
function enqueue_plugin_scripts() {

    if ( ! current_user_can( delete_posts ) ) {
        wp_register_script(
            'dashboard-hide',
    		plugins_url( 'inc/js/dashboard-hide.js', __FILE__ ),
    		array( 'jquery' )
    	);
        wp_enqueue_script('dashboard-hide');
    }

}
add_action( 'admin_enqueue_scripts', 'enqueue_plugin_scripts' );


// META BOX FUNCTIONS


function peer_reviewer_meta_box() {

    // if ( ! current_user_can( delete_posts ) ) {

        add_meta_box( 'peer_reviewer_info_metabox',
                      'Enter Expert Peer Reviewer Information Here',
                      'add_peer_reviewer_info_metabox',
                      'page',
                      'normal',
                      'high'
                    );

    // }

}
add_action( 'add_meta_boxes', 'peer_reviewer_meta_box' );

function add_peer_reviewer_info_metabox( $page ) {

    wp_nonce_field( 'myplugin_save_meta_box_data', 'myplugin_meta_box_nonce' );

	/*
	 * Use get_post_meta() to retrieve an existing value
	 * from the database and use the value for the form.
	 */
	$value = get_post_meta( $page->ID, '_my_meta_value_key', true );

    // TODO: MOVE TO EXTERNAL FILE AND REQUIRE HERE
	echo '<label for="myplugin_new_field">';
	_e( 'Description for this field', 'myplugin_textdomain' );
	echo '</label> ';
	echo '<input type="text" id="myplugin_new_field" name="myplugin_new_field" value="' . esc_attr( $value ) . '" size="25" />';

}



?>
