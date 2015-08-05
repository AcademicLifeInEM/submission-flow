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

/**
 *
 *   ___ __  __ ____   ___  ____ _____  _    _   _ _____ 
 *  |_ _|  \/  |  _ \ / _ \|  _ \_   _|/ \  | \ | |_   _|
 *   | || |\/| | |_) | | | | |_) || | / _ \ |  \| | | |
 *   | || |  | |  __/| |_| |  _ < | |/ ___ \| |\  | | |
 *  |___|_|  |_|_|    \___/|_| \_\|_/_/   \_\_| \_| |_|
 *
 * NOTE: THIS PLUGIN DEPENDS ON THE MODIFICATION OF THE COAUTHORS PLUS
 * PLUGIN. BE SURE TO COMMENT OUT LINES 1041-1049. (version: 3.1.1)
 */

////////////////////
// PLUGIN GLOBALS //
////////////////////

/**
 * ADD COPYEDITORS EMAIL ADDRESSES TO THE BELOW LIST
 */

$submission_editor_email = 'dereksifford@gmail.com';
$copyeditor_email_list = array ( 'dereksifford@gmail.com', 'dereksifford@gmail.com', 'dereksifford@gmail.com' );


// Enqueue Javascript
function enqueue_plugin_scripts() {

    if ( current_user_can( 'subscriber' ) ) {
        /** Register scripts */
        wp_register_script('dashboard-hide', plugins_url( 'inc/js/dashboard-hide.js', __FILE__ ), array( 'jquery' ) );
        wp_register_script('form-handler', plugins_url( 'inc/js/form-handling.js', __FILE__ ), array( 'jquery' ) );

        /** Enqueue scripts */
        wp_enqueue_script( 'dashboard-hide' );
        wp_enqueue_script( 'form-handler' );
    } else {

        global $post;

        $submission_page = get_page_by_title( 'New Submission' );

        $parent_page = $post->post_parent;

        if ( $parent_page == $submission_page->ID ) {

            wp_register_script( 'copyeditor-views', plugins_url( 'inc/js/copyeditor-views.js', __FILE__ ), array( 'jquery' ) );

            wp_enqueue_script( 'copyeditor-views' );
        }

    }

}
add_action( 'admin_enqueue_scripts', 'enqueue_plugin_scripts' );


// CUSTOMIZE CAPABILITIES FOR SUBSCRIBERS
function adjust_subscriber_capabilities() {

    $role = get_role( 'subscriber' );

    $role->add_cap( 'edit_pages' );
    $role->add_cap( 'upload_files' );
    $role->add_cap( 'edit_published_pages' );

}
add_action( 'admin_init', 'adjust_subscriber_capabilities' );


// ONLY ALLOW SUBSCRIBERS TO SEE THEIR OWN MEDIA UPLOADS
function users_own_attachments( $wp_query_obj ) {

    global $current_user, $pagenow;

    if( !is_a( $current_user, 'WP_User') )
        return;

    if( ( 'upload.php' != $pagenow ) && ( ( 'admin-ajax.php' != $pagenow ) || ( $_REQUEST['action'] != 'query-attachments' ) ) )
        return;

    if( !current_user_can('delete_pages') )
        $wp_query_obj->set('author', $current_user->id );

    return;
}
add_action('pre_get_posts','users_own_attachments');

//////////////////////////////////////
// ---------- META BOXES ---------- //
//////////////////////////////////////


/**
 * INSTANTIATE META BOXES
 */

// Peer reviewer information meta box
function peer_reviewer_meta_box() {

    if ( current_user_can( 'subscriber' ) ) {
        add_meta_box( 'peer_reviewer_meta_box', 'Expert Peer Reviewer Information', 'add_peer_reviewer_meta_box', 'page', 'side', 'high');
    }
}
add_action( 'add_meta_boxes', 'peer_reviewer_meta_box' );


// Co-author meta box
function coauthor_meta_box() {

    if ( current_user_can( 'subscriber' ) ) {
        add_meta_box( 'coauthor_meta_box', 'Co-author Details', 'add_coauthor_meta_box', 'page', 'advanced', 'high');
    }
}
add_action( 'add_meta_boxes', 'coauthor_meta_box' );


/**
 * ADD META BOXES TO PAGE EDIT SCREEN
 */

// Peer reviewer information meta box
function add_peer_reviewer_meta_box( $post ) {

    wp_nonce_field( basename( __file__ ), 'submission_PR_info' );

	$values = get_post_custom( $post->ID );

    $PR_first_name = $values['PR_first_name'][0];
    $PR_last_name = $values['PR_last_name'][0];
    $PR_email = $values['PR_email'][0];
    $PR_background_info = $values['PR_background_info'][0];

    require('/inc/meta-peer-reviewer-info.php');

}


// Co-author meta box
function add_coauthor_meta_box( $post ) {

    wp_nonce_field( basename( __file__ ), 'submission_coauthors' );

    $values = get_post_custom( $post->ID );

    for ($i=1; $i < 4; $i++) {

        ${'coauthor_' . $i . '_first_name'} = $values['coauthor_' . $i . '_first_name'][0];
        ${'coauthor_' . $i . '_last_name'} = $values['coauthor_' . $i . '_last_name'][0];
        ${'coauthor_' . $i . '_email'} = $values['coauthor_' . $i . '_email'][0];
        ${'coauthor_' . $i . '_twitter'} = $values['coauthor_' . $i . '_twitter'][0];
        ${'coauthor_' . $i . '_background'} = $values['coauthor_' . $i . '_background'][0];


    }

    require('/inc/meta-coauthors.php');

}



/**
 * SAVE META BOX FIELDS TO DATABASE
 */

// Peer review info meta box
function save_peer_review_info_meta( $post_id ) {

    $is_autosave = wp_is_post_autosave( $post_id );
	$is_revision = wp_is_post_revision( $post_id );
	$is_valid_nonce = ( isset( $_POST[ 'submission_PR_info' ] ) && wp_verify_nonce( $_POST[ 'submission_PR_info' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
			return;
	}

    if ( isset( $_POST['PR_first_name']) ) {

        update_post_meta( $post_id, 'PR_first_name',  $_POST['PR_first_name'] );
        update_post_meta( $post_id, 'PR_last_name',  $_POST['PR_last_name'] );
        update_post_meta( $post_id, 'PR_email',  $_POST['PR_email'] );
        update_post_meta( $post_id, 'PR_background_info',  $_POST['PR_background_info'] );

    }

}
add_action( 'save_post', 'save_peer_review_info_meta' );

// Co-author Details meta box
function save_coauthor_details_meta( $post_id ) {

    $is_autosave    = wp_is_post_autosave( $post_id );
	$is_revision    = wp_is_post_revision( $post_id );
	$is_valid_nonce = ( isset( $_POST[ 'submission_PR_info' ] ) && wp_verify_nonce( $_POST[ 'submission_PR_info' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
			return;
	}

    if ( current_user_can( 'subscriber' ) ) {

        for ($i=1; $i < 5; $i++) {

            if ( isset( $_POST['coauthor_' . $i . '_first_name'] ) ) {

                update_post_meta( $post_id, 'coauthor_' . $i . '_first_name',  $_POST['coauthor_' . $i . '_first_name'] );
                update_post_meta( $post_id, 'coauthor_' . $i . '_last_name',  $_POST['coauthor_' . $i . '_last_name'] );
                update_post_meta( $post_id, 'coauthor_' . $i . '_email',  $_POST['coauthor_' . $i . '_email'] );
                update_post_meta( $post_id, 'coauthor_' . $i . '_twitter',  $_POST['coauthor_' . $i . '_twitter'] );
                update_post_meta( $post_id, 'coauthor_' . $i . '_background',  $_POST['coauthor_' . $i . '_background'] );

            }

            ///////////////////////////////////////
            // ----- CREATE NEW USER LOGIC ----- //
            ///////////////////////////////////////


            $username = $_POST['coauthor_' . $i . '_first_name'] . '.' . $_POST['coauthor_' . $i . '_last_name'];

            // IF USER DOES NOT ALREADY EXIST... CREATE NEW USER
            if ( get_user_by( 'email', $_POST['coauthor_' . $i . '_email'] ) == '' && get_user_by( 'login', $username ) == '' ) {

                // Corrects error where a user with the login '.' would be created
                if ($username == '.') {
                    break;
                }

                $password = 'ALiEMSubmissionUser';
                wp_create_user( $username, $password );

            }

        }
    }

}
add_action( 'save_post', 'save_coauthor_details_meta' );




// RETRIEVE META FOR COPYEDITORS
function display_meta_for_copyeditors() {

    global $post;

    $submission_page = get_page_by_title( 'New Submission' );
    $parent_page = $post->post_parent;


    if ( $parent_page == $submission_page->ID ) {

        $post_meta = get_post_custom( $post->ID );

        $PR_first_name = $post_meta['PR_first_name'][0];
        $PR_last_name = $post_meta['PR_last_name'][0];
        $PR_email = $post_meta['PR_email'][0];
        $PR_background_info = $post_meta['PR_background_info'][0];

        for ($i=1; $i < 4; $i++) {

            ${'coauthor_' . $i . '_first_name'} = $post_meta['coauthor_' . $i . '_first_name'][0];
            ${'coauthor_' . $i . '_last_name'} = $post_meta['coauthor_' . $i . '_last_name'][0];
            ${'coauthor_' . $i . '_email'} = $post_meta['coauthor_' . $i . '_email'][0];
            ${'coauthor_' . $i . '_twitter'} = $post_meta['coauthor_' . $i . '_twitter'][0];
            ${'coauthor_' . $i . '_background'} = $post_meta['coauthor_' . $i . '_background'][0];


        }

        add_meta_box( 'peer_reviewer_meta_box', 'Expert Peer Reviewer Information', 'add_peer_reviewer_meta_box', 'page', 'side', 'high' );
        add_meta_box( 'coauthor_meta_box', 'Co-author Details', 'add_coauthor_meta_box', 'page', 'advanced', 'high');
    }

}
add_action( 'admin_head', 'display_meta_for_copyeditors' );



/**
 * END META BOX FUNCTIONS
 */


/**
 * BEGIN EMAIL FUNCTION -- EMAIL COPYEDITORS
 */
function send_email_to_copyeditor( $post ) {

    global $copyeditor_email_list, $submission_editor_email;

    $testinggg = 'asfasfasdasdfasdasfas8757asdf85@98yadsf67yasd.com';

    if ( current_user_can('subscriber') ) {

        if ( get_option('copyeditor_rotation') == false ) {

            add_option( 'copyeditor_rotation', 0 );

        }

        $which_copyeditor = get_option( 'copyeditor_rotation' );
        $headers = array(
            'From: ALiEM <mlin@aliem.com>;',
            'Cc: ' . $submission_editor_email,
        );

// TODO: FINALIZE EMAIL MESSAGE

		$user_info = get_userdata($post->post_author);

        $submitter_name = ( $user_info->display_name == '' ? $user_info->nicename : $user_info->display_name );

		$subject = 'New Submission: ' . $post->post_title;
		$message = 'A post "' . $post->post_title . '" by ' . $submitter_name . ' was submitted for review at ' . wp_get_shortlink ($post->ID) . '&preview=true. Please proof.';

        wp_mail( $copyeditor_email_list[$which_copyeditor], $subject, $message, $headers );

        if ( $which_copyeditor < count($copyeditor_email_list) - 1 ) {

            $which_copyeditor++;
            update_option( 'copyeditor_rotation', $which_copyeditor );

        } else {

            update_option( 'copyeditor_rotation', 0 );

        }

	}

}
// TODO: ADD TO THE ABOVE FUNCTION --- AUTOMATICALLY SET PARENT PAGE
add_action( 'draft_to_pending', 'send_email_to_copyeditor' );


/**
 * END EMAIL FUNCTION -- EMAIL COPYEDITORS
 */


 /**
  * BEGIN EMAIL FUNCTION -- EMAIL EXPERT PEER REVIEWER
  */

function send_email_to_peer_reviewer() {

    global $post, $submission_editor_email;

    $submission_page = get_page_by_title( 'New Submission' );
    $parent_page = $post->post_parent;


    if ( $parent_page == $submission_page->ID ) {

        $post_meta = get_post_custom( $post->ID );

        $PR_first_name = $post_meta['PR_first_name'][0];
        $PR_last_name = $post_meta['PR_last_name'][0];
        $PR_email = $post_meta['PR_email'][0];
        $PR_background_info = $post_meta['PR_background_info'][0];

// TODO: UPDATE EMAIL MESSAGE FOR EXPERT REVIEWER (DISCUSS W/ FELLOWS)

        if ($PR_email !== '') {

            $user_info = get_userdata($post->post_author);
    		$subject = 'New Submission: ' . $user_info->user_nicename . ' submitted a post';
    		$message = 'A post "' . $post->post_title . '" by ' . $user_info->user_nicename . ' was submitted for review at ' . wp_get_shortlink ($post->ID) . '&preview=true. Please proof.';
            $headers = array(
                'From: ALiEM <mlin@aliem.com>;',
                'Cc: ' . $submission_editor_email,
            );
            wp_mail( $PR_email, $subject, $message, $headers );

        }



    }

}
add_action( 'pending_to_publish', 'send_email_to_peer_reviewer' );



?>
