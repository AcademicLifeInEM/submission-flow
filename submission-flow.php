<?php

/*
 *	Plugin Name: ALiEM Submission Flow
 *	Plugin URI: null
 *	Description: Internal plugin to enhance new submission flow
 *	Version: 0.1.1
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
 * NOTE: THIS PLUGIN DEPENDS ON THE MODIFICATION OF THE FOLLWOING PLUGINS:
 *
 * 1) CO-AUTHORS PLUS:
 *    - COMMENT OUT LINES 1041-1049. (version: 3.1.1)
 * 2) FANCIEST AUTHOR BOX: (/includes/ts-fab-user-settings.php)
 *    - LINE 21: Comment out whole line
 *    - LINE 137: Comment out whole line ('<?php } // end if ?>')
 * 3) DEPENDENCY: TablePress -- Table with the title 'Staging Area: Blog Posts in Progress'
 */

////////////////////
// PLUGIN GLOBALS //
////////////////////

/**
 * ADD COPYEDITORS' EMAIL ADDRESSES TO THE BELOW LIST
 */

$submission_editor_email = 'dereksifford@gmail.com';
$copyeditor_email_list = array( 'copyeditor1@maildrop.cc', 'copyeditor2@maildrop.cc', 'copyeditor3@maildrop.cc' );


// Enqueue Javascript
function enqueue_plugin_scripts() {

    wp_register_style( 'submission-flow-css', plugins_url( 'inc/submission-flow.css', __FILE__ ) );
    wp_enqueue_style( 'submission-flow-css' );

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

function enqueue_plugin_frontend_scripts() {
    if ( current_user_can( 'subscriber' ) ) {
        wp_register_script('frontend-hide', plugins_url( 'inc/js/frontend-hide.js', __FILE__ ), array( 'jquery' ) );
        wp_enqueue_script( 'frontend-hide' );
    }
}
add_action( 'wp_enqueue_scripts', 'enqueue_plugin_frontend_scripts' );


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

    if( current_user_can('subscriber') )
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

    for ( $i = 1; $i < 3; $i++ ) {

        ${'PR_first_name_' . $i} = $values['PR_first_name_' . $i][0];
        ${'PR_last_name_' . $i} = $values['PR_last_name_' . $i][0];
        ${'PR_email_' . $i} = $values['PR_email_' . $i][0];
        ${'PR_background_info_' . $i} = $values['PR_background_info_' . $i][0];

    }


    require( 'inc/meta-peer-reviewer-info.php' );

}


// Co-author meta box
function add_coauthor_meta_box( $post ) {

    wp_nonce_field( basename( __file__ ), 'submission_coauthors' );

    $values = get_post_custom( $post->ID );

    for ( $i=1; $i < 5; $i++ ) {

        ${'coauthor_' . $i . '_first_name'} = $values['coauthor_' . $i . '_first_name'][0];
        ${'coauthor_' . $i . '_last_name'} = $values['coauthor_' . $i . '_last_name'][0];
        ${'coauthor_' . $i . '_email'} = $values['coauthor_' . $i . '_email'][0];
        ${'coauthor_' . $i . '_twitter'} = $values['coauthor_' . $i . '_twitter'][0];
        ${'coauthor_' . $i . '_background'} = $values['coauthor_' . $i . '_background'][0];


    }

    require( 'inc/meta-coauthors.php' );

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
// FIXME:
    if ( isset( $_POST['PR_first_name_1']) ) {

        update_post_meta( $post_id, 'PR_first_name_1',  $_POST['PR_first_name_1'] );
        update_post_meta( $post_id, 'PR_last_name_1',  $_POST['PR_last_name_1'] );
        update_post_meta( $post_id, 'PR_email_1',  $_POST['PR_email_1'] );
        update_post_meta( $post_id, 'PR_background_info_1',  $_POST['PR_background_info_1'] );

    }

    if ( isset( $_POST['PR_first_name_2']) ) {

        update_post_meta( $post_id, 'PR_first_name_2',  $_POST['PR_first_name_2'] );
        update_post_meta( $post_id, 'PR_last_name_2',  $_POST['PR_last_name_2'] );
        update_post_meta( $post_id, 'PR_email_2',  $_POST['PR_email_2'] );
        update_post_meta( $post_id, 'PR_background_info_2',  $_POST['PR_background_info_2'] );

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

        for ( $i = 1; $i < 5; $i++ ) {

            if ( isset( $_POST['coauthor_' . $i . '_first_name'] ) ) {

                update_post_meta( $post_id, 'coauthor_' . $i . '_first_name',  $_POST['coauthor_' . $i . '_first_name'] );
                update_post_meta( $post_id, 'coauthor_' . $i . '_last_name',  $_POST['coauthor_' . $i . '_last_name'] );
                update_post_meta( $post_id, 'coauthor_' . $i . '_email',  $_POST['coauthor_' . $i . '_email'] );
                update_post_meta( $post_id, 'coauthor_' . $i . '_twitter',  $_POST['coauthor_' . $i . '_twitter'] );
                update_post_meta( $post_id, 'coauthor_' . $i . '_background',  wpautop( $_POST['coauthor_' . $i . '_background'] ) );

            }

            ///////////////////////////////////////
            // ----- CREATE NEW USER LOGIC ----- //
            ///////////////////////////////////////

            $username = ucwords( $_POST['coauthor_' . $i . '_first_name'] ) . '.' . preg_replace('/\s+/', '.', ucwords( $_POST['coauthor_' . $i . '_last_name'] ) );
// TODO: if the user exists, update him/her with new background info, etc (also consider adding <br>)

            /**
             * IF  : the user does not already exist, then create a new user
             * ELSE: update the existing user with the information provided
             */

            if ( get_user_by( 'email', strtolower( $_POST['coauthor_' . $i . '_email'] ) ) == '' && get_user_by( 'login', $username ) == '' ) {

                // Corrects error where a user with the login '.' would be created
                // FIXME:
                if ($username == '.') {
                    break;
                }

                $userdata = array(
                    'user_pass' => 'ALiEMSubmissionUser',
                    'user_login' => $username,
                    'user_email' => $_POST['coauthor_' . $i . '_email'],
                    'display_name' => ucwords( $_POST['coauthor_' . $i . '_first_name'] ) . ' ' . ucwords( $_POST['coauthor_' . $i . '_last_name'] ),
                    'first_name' => ucwords( $_POST['coauthor_' . $i . '_first_name'] ),
                    'last_name' => ucwords( $_POST['coauthor_' . $i . '_last_name'] ),
                    'description' => $_POST['coauthor_' . $i . '_background'],
                    'role' => 'subscriber',
                );

                $new_user_id = wp_insert_user( $userdata );
                update_user_meta( $new_user_id, 'ts_fab_twitter', $_POST['coauthor_' . $i . '_twitter'] );

            } else {

                $the_existing_user = get_user_by( 'email', strtolower( $_POST['coauthor_' . $i . '_email'] ) );

                $userdata = array(
                    'ID' => $the_existing_user->ID,
                    'user_login' => $username,
                    'description' => $_POST['coauthor_' . $i . '_background'],
                );
                wp_update_user( $userdata );
                update_user_meta( $the_existing_user->ID, 'ts_fab_twitter', $_POST['coauthor_' . $i . '_twitter'] );
            }
        }
    }
}
add_action( 'save_post', 'save_coauthor_details_meta' );



// DISPLAY META FOR COPYEDITORS
function display_meta_for_copyeditors() {

    global $post;

    $submission_page = get_page_by_title( 'New Submission' );
    $parent_page = $post->post_parent;


    if ( $parent_page == $submission_page->ID ) {

        $post_meta = get_post_custom( $post->ID );

        for ( $i = 1; $i < 3; $i++ ) {

            ${'PR_first_name_' . $i} = $post_meta['PR_first_name_' . $i][0];
            ${'PR_last_name_' . $i} = $post_meta['PR_last_name_' . $i][0];
            ${'PR_email_' . $i} = $post_meta['PR_email_' . $i][0];
            ${'PR_background_info_' . $i} = $post_meta['PR_background_info_' . $i][0];

        }

        for ( $i = 1; $i < 5; $i++ ) {

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
add_action( 'add_meta_boxes', 'display_meta_for_copyeditors' );

/**
 * END META BOX FUNCTIONS
 */


/**
 * BEGIN EMAIL FUNCTIONS
 */

function send_email_to_copyeditor( $post ) {

    if ( current_user_can('subscriber') ) {

        global $copyeditor_email_list, $submission_editor_email;

        // SET PARENT PAGE TO 'New Submission'
        $submission_page = get_page_by_title( 'New Submission' );
        $updated_post = array(
            'ID' => $post->ID,
            'post_parent' => $submission_page->ID,
        );
        wp_update_post( $updated_post );


        // ADD ROW TO TABLEPRESS TABLE
        $post_custom = get_post_custom( $post->ID );
        $first_author = get_user_by( 'id', $post->post_author );
        $authors = $first_author->first_name . ' ' . $first_author->last_name;
        $authors .= isset( $post_custom['coauthor_1_first_name'] ) ? ', ' . $post_custom['coauthor_1_first_name'][0] . ' ' . $post_custom['coauthor_1_last_name'][0] : '';
        $authors .= isset( $post_custom['coauthor_2_first_name'] ) ? ', ' . $post_custom['coauthor_2_first_name'][0] . ' ' . $post_custom['coauthor_2_last_name'][0] : '';
        $authors .= isset( $post_custom['coauthor_3_first_name'] ) ? ', ' . $post_custom['coauthor_3_first_name'][0] . ' ' . $post_custom['coauthor_3_last_name'][0] : '';
        $authors .= isset( $post_custom['coauthor_4_first_name'] ) ? ', ' . $post_custom['coauthor_4_first_name'][0] . ' ' . $post_custom['coauthor_4_last_name'][0] : '';
        tablepress_add( $authors, $post->post_title );


        // Is 'copyeditor_rotation' defined yet in the database? If not, start at 0
        if ( get_option('copyeditor_rotation') == false ) {
            add_option( 'copyeditor_rotation', 0 );
        }

        // Variable to hold current number in copyeditor rotation
        $which_copyeditor = get_option( 'copyeditor_rotation' );

        // Get author details, then set display name for email
		$user_info = get_userdata($post->post_author);
        $submitter_name = ( $user_info->display_name == '' ? $user_info->nicename : $user_info->display_name );

        // Set headers, subject, and message for email
        $headers = array(
            'From: ALiEM <submission@aliem.com>;',
            'Cc: ' . $submission_editor_email,
        );
        $subject = 'New Submission: ' . $post->post_title;
// TODO: FINALIZE EMAIL MESSAGE
        $message = 'A post "' . $post->post_title . '" by ' . $submitter_name . ' was submitted for review at ' . wp_get_shortlink ($post->ID) . '&preview=true. Please proof.';

        // Send the email
        wp_mail( $copyeditor_email_list[$which_copyeditor], $subject, $message, $headers );

        // If the copyeditor rotation is not at the end, increment by 1. Otherwise, set back to 0.
        if ( $which_copyeditor < count($copyeditor_email_list) - 1 ) {
            $which_copyeditor++;
            update_option( 'copyeditor_rotation', $which_copyeditor );
        } else {
            update_option( 'copyeditor_rotation', 0 );
        }

	}

}
add_action( 'draft_to_pending', 'send_email_to_copyeditor' );


function send_email_to_peer_reviewer() {

    global $post, $submission_editor_email;

    $submission_page = get_page_by_title( 'New Submission' );
    $parent_page = $post->post_parent;


    if ( $parent_page == $submission_page->ID ) {

        $post_meta = get_post_custom( $post->ID );

        $PR_first_name_1 = $post_meta['PR_first_name_1'][0];
        $PR_last_name_1 = $post_meta['PR_last_name_1'][0];
        $PR_email_1 = $post_meta['PR_email_1'][0];
        $PR_background_info_1 = $post_meta['PR_background_info_1'][0];

        $PR_first_name_2 = $post_meta['PR_first_name_2'][0];
        $PR_last_name_2 = $post_meta['PR_last_name_2'][0];
        $PR_email_2 = $post_meta['PR_email_2'][0];
        $PR_background_info_2 = $post_meta['PR_background_info_2'][0];

// TODO: UPDATE EMAIL MESSAGE FOR EXPERT REVIEWER (DISCUSS W/ FELLOWS)

        if ($PR_email_1 !== '') {

            $user_info = get_userdata($post->post_author);
    		$subject = 'New Submission: ' . $user_info->user_nicename . ' submitted a post';
    		$message = 'A post "' . $post->post_title . '" by ' . $user_info->user_nicename . ' was submitted for review at ' . wp_get_shortlink ($post->ID) . '&preview=true. Please proof.';
            $headers = array(
                'From: ALiEM <submission@aliem.com>;',
                'Cc: ' . $submission_editor_email,
            );
            wp_mail( $PR_email_1, $subject, $message, $headers );

        }

        if ($PR_email_2 !== '') {

    		$message = 'A post "' . $post->post_title . '" by ' . $user_info->user_nicename . ' was submitted for review at ' . wp_get_shortlink ($post->ID) . '&preview=true. Please proof.';
            $headers = array(
                'From: ALiEM <submission@aliem.com>;',
                'Cc: ' . $submission_editor_email,
            );
            wp_mail( $PR_email_2, $subject, $message, $headers );

        }

    }
}
add_action( 'pending_to_publish', 'send_email_to_peer_reviewer' );

/**
 * END EMAIL FUNCTIONS
 */

// CONVERT FINALIZED SUBMISSION 'PAGE' INTO A 'POST' ON STATUS CHANGE (PUBLISH TO DRAFT)
function finalize_submission( $post ) {

    $submission_page = get_page_by_title( 'New Submission' );
    $parent_page = $post->post_parent;

    if ( $parent_page == $submission_page->ID && !user_can( 'subscriber' ) ) {

        $page_to_post = $post;
        $page_to_post->post_type = 'post';
        wp_update_post( $page_to_post );
    }

}
add_action('publish_to_draft', 'finalize_submission');


/**
 * HELPER FUNCTIONS
 */
function parse_html_chars( $input ) {
    $output = preg_replace( "/<p>/", "", $input );
    $output = preg_replace( "/<br \\/>/", "\r", $output );
    $output = preg_replace( "/<\\/p>/", "\n", $output );
    return( trim( $output ) );
}


// ADD ROW TO TABLEPRESS SPREADSHEET
function tablepress_add( $author, $title ) {

    global $wpdb;

    $posttitle = 'Staging Area: Blog Posts in Progress';
    $postid = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_title = '" . $posttitle . "'" );

    $tablepress = get_post( $postid );
    $tabledata = json_decode( $tablepress->post_content );

    $date_today = date('m/d/Y');

    $new_row = array(
        $date_today,
        $author,
        $title,
    );

    array_push( $tabledata, $new_row );

    $tabledata = json_encode( $tabledata );

    $updated_post = array(
        'ID' => $postid,
        'post_content' => $tabledata
    );
    wp_update_post( $updated_post );

}


?>
