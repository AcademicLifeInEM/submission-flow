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
        /** Register scripts */
        wp_register_script('dashboard-hide', plugins_url( 'inc/js/dashboard-hide.js', __FILE__ ), array( 'jquery' ) );
        wp_register_script('form-handler', plugins_url( 'inc/js/form-handling.js', __FILE__ ), array( 'jquery' ) );

        /** Enqueue scripts */
        wp_enqueue_script( 'dashboard-hide' );
        wp_enqueue_script( 'form-handler' );
    }

}
add_action( 'admin_enqueue_scripts', 'enqueue_plugin_scripts' );


/**
 * BEGIN META BOX FUNCTIONS
 */

function peer_reviewer_meta_box() {

    if ( ! current_user_can( delete_posts ) ) {
        add_meta_box( 'peer_reviewer_info_metabox', 'Expert Peer Reviewer Information', 'add_peer_reviewer_info_metabox', 'page', 'side', 'high');
    }
}
add_action( 'add_meta_boxes', 'peer_reviewer_meta_box' );



// ADD META BOX TO PAGE
function add_peer_reviewer_info_metabox( $post ) {

    wp_nonce_field( basename( __file__ ), 'submission_PR_info' );

	/*
	 * Use get_post_meta() to retrieve an existing value
	 * from the database and use the value for the form.
	 */
	$values = get_post_custom( $post->ID );

    $PR_first_name = $values['PR_first_name'][0];
    $PR_last_name = $values['PR_last_name'][0];
    $PR_email = $values['PR_email'][0];
    $PR_background_info = $values['PR_background_info'][0];

    require('/inc/meta-peer-reviewer-info.php');

}

// SAVE META BOX VALUES
function save_peer_review_info_meta( $post_id ) {

    $is_autosave = wp_is_post_autosave( $post_id );
	$is_revision = wp_is_post_revision( $post_id );
	$is_valid_nonce = ( isset( $_POST[ 'submission_PR_info' ] ) && wp_verify_nonce( $_POST[ 'submission_PR_info' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
			return;
	}

    // TODO: ADD isset to individual elements and remove the conditional
    if ( isset( $_POST['PR_first_name']) ) {

        update_post_meta( $post_id, 'PR_first_name',  $_POST['PR_first_name'] );
        update_post_meta( $post_id, 'PR_last_name',  $_POST['PR_last_name'] );
        update_post_meta( $post_id, 'PR_email',  $_POST['PR_email'] );
        update_post_meta( $post_id, 'PR_background_info',  $_POST['PR_background_info'] );

    }

}
add_action( 'save_post', 'save_peer_review_info_meta' );

/**
 * END META BOX FUNCTIONS
 */


/**
 * BEGIN EMAIL FUNCTION -- EMAIL REVIEWERS
 */

function send_email_to_copyeditor( $post ) {

    if ( current_user_can('subscriber') ) {
		$user_info = get_userdata ($post->post_author);
		$strTo = array ('dereksifford@gmail.com');
		$strSubject = 'Your website name: ' . $user_info->user_nicename . ' submitted a post';
		$strMessage = 'A post "' . $post->post_title . '" by ' . $user_info->user_nicename . ' was submitted for review at ' . wp_get_shortlink ($post->ID) . '&preview=true. Please proof.';
		wp_mail( $strTo, $strSubject, $strMessage );
	}

}





?>
