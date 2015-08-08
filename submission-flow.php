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
// Testing purposes
$copyeditor_email_list = array(
                            [
                                'name'  => 'Dr. Bryan Hayes',
                                'email' => 'dereksifford@gmail.com',
                            ],
                            [
                                'name'  => 'Dr. Teresa Chan',
                                'email' => 'dereksifford@gmail.com',
                            ],
                            [
                                'name'  => 'Dr. Howie Mell',
                                'email' => 'dereksifford@gmail.com',
                            ],
                            [
                                'name'  => 'Dr. Nikita Joshi',
                                'email' => 'dereksifford@gmail.com',
                            ],
                            [
                                'name'  => 'Dr. Sameed Shaikh',
                                'email' => 'dereksifford@gmail.com',
                            ],
                            [
                                'name'  => 'Dr. Matt Zuckerman',
                                'email' => 'dereksifford@gmail.com',
                            ],
                            [
                                'name'  => 'Dr. Michelle Lin',
                                'email' => 'dereksifford@gmail.com',
                            ],
                            [
                                'name'  => 'Dr. Matthew Klein',
                                'email' => 'dereksifford@gmail.com',
                            ],
                            [
                                'name'  => 'Dr. Alissa Mussell',
                                'email' => 'dereksifford@gmail.com',
                            ]
                        );

// $copyeditor_email_list = array(
//                             [
//                                 'name'  => 'Dr. Bryan Hayes',
//                                 'email' => 'bryanhayes13@gmail.com',
//                             ],
//                             [
//                                 'name'  => 'Dr. Teresa Chan',
//                                 'email' => 'teresamchan@gmail.com',
//                             ],
//                             [
//                                 'name'  => 'Dr. Howie Mell',
//                                 'email' => 'howie.mell@gmail.com',
//                             ],
//                             [
//                                 'name'  => 'Dr. Nikita Joshi',
//                                 'email' => 'njoshi@aliem.com',
//                             ],
//                             [
//                                 'name'  => 'Dr. Sameed Shaikh',
//                                 'email' => 'samshaikh@gmail.com',
//                             ],
//                             [
//                                 'name'  => 'Dr. Matt Zuckerman',
//                                 'email' => 'mzuckerm@gmail.com',
//                             ],
//                             [
//                                 'name'  => 'Dr. Michelle Lin',
//                                 'email' => 'mlin@aliem.com',
//                             ],
//                             [
//                                 'name'  => 'Dr. Matthew Klein',
//                                 'email' => 'matthew.richard.klein@gmail.com',
//                             ],
//                             [
//                                 'name'  => 'Dr. Alissa Mussell',
//                                 'email' => 'r.alissa.mussell@gmail.com',
//                             ]
//                         );


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

function draft_submitted_by_author( $post ) {

    if ( current_user_can('subscriber') ) {

        global $copyeditor_email_list, $submission_editor_email;

        // SET PARENT PAGE
        $submission_page = get_page_by_title( 'New Submission' );

        // ADD TITLE AND DRAFT IMAGE TO TOP OF DRAFT
        $updated_content = '<img class="aligncenter size-full wp-image-16521" src="http://www.aliem.com/wp-content/uploads/Draft.jpg" alt="Draft" width="400" height="116" /><h1>' . $post->post_title . '</h1>' . $post->post_content;
        $updated_post = array(
            'ID' => $post->ID,
            'post_parent' => $submission_page->ID,
            'post_content' => $updated_content,
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

        //////////////////////////////////
        // PREPARE VARIABLES FOR EMAILS //
        //////////////////////////////////

        // Is 'copyeditor_rotation' defined yet in the database? If not, start at 0
        if ( get_option('copyeditor_rotation') == false ) {
            add_option( 'copyeditor_rotation', 0 );
        }

        // Variable to hold current number in copyeditor rotation
        $which_copyeditor = get_option( 'copyeditor_rotation' );

        // Get author details, then set display name for email
		$user_info = get_userdata($post->post_author);
        $submitter_name = ( $user_info->display_name == '' ? $user_info->nicename : $user_info->display_name );

        /////////////////////////////////////////
        // PREPARE AND SEND CONFIRMATION EMAIL //
        /////////////////////////////////////////

        /**
         * Set the recipients to an array of all the authors listed.
         * After, if the array value is empty, remove it.
         */
        $recipients = array(
            $user_info->user_email,
            $post_custom['coauthor_1_email'][0],
            $post_custom['coauthor_2_email'][0],
            $post_custom['coauthor_3_email'][0],
            $post_custom['coauthor_4_email'][0],
        );
        foreach($recipients as $key => $value) {
        	if($value == '' || $value === 'undefined') {
            	unset($recipients[$key]);
            }
		}

        $headers = array(
            'From: ALiEM Team <submission@aliem.com>',
            'Cc: ' . $submission_editor_email,
            'Content-Type: text/html',
            'charset=UTF-8',
        );
        $subject = 'Submission Received: "' . $post->post_title . '"';
        $message = "<img src='http://aliem.com/wp-content/uploads/2013/05/logo-horizontal-color.png'><br>" .
                   "<div style='font-size: 18px;'>" .
                   "<p>Thank you for your interest in posting content to ALiEM!</p>" .
                   "<p>Your copyeditor, " . $copyeditor_email_list[$which_copyeditor]['name'] . ", has been notified and will begin " .
                   "proofing shortly. Once the proofing has been completed, you will be notified via email. " .
                   "At that time, we will also notify your selected Expert Peer Reviewer(s) that the draft is ready for their review. " .
                   "As a reminder, we assume that you have already spoken with your selected Expert Peer Reviewers and they have agreed to participate.</p>" .
                   "<p>If you have any questions, please feel free to contact your copyeditor or our Submission Editor via email at any time. " .
                   "For convienience, their contact information is listed below.</p>" .
                   "<ul><li><strong>Copyeditor</strong>: " . $copyeditor_email_list[$which_copyeditor]['name'] . ", " . $copyeditor_email_list[$which_copyeditor]['email'] . "</li>" .
                   "<li><strong>Submission Editor</strong>: Derek Sifford, submission@aliem.com</li></ul>" .
                   "<p>Thank you again for your interest. We look forward to working with you!</p>" .
                   "<p>Kind regards,<br>The ALiEM Team</p>";
        wp_mail( $recipients, $subject, $message, $headers );

        ///////////////////////////////////////
        // PREPARE AND SEND COPYEDITOR EMAIL //
        ///////////////////////////////////////

        $subject = 'ALiEM Copyedit Request: "' . $post->post_title . '"';
        // Regex - Extract Copyeditor's first name
        preg_match( "/(?:\\w+. )(\\w+)/", $copyeditor_email_list[$which_copyeditor]['name'], $copyeditor_first_name );

        $message = "<img src='http://aliem.com/wp-content/uploads/2013/05/logo-horizontal-color.png'><br>" .
                   "<div style='font-size: 18px;'>" .
                   "<p>Hi " . $copyeditor_first_name[1] . "!</p>" .
                   "<p>A submission titled \"<a href='" . $post->guid . "'>" . $post->post_title .
                   "</a>\" has been submitted for review by " . $submitter_name . ".</p>" .
                   "<p>Please copyedit at your earliest convienience.</p></div>";

        wp_mail( $copyeditor_email_list[$which_copyeditor]['email'], $subject, $message, $headers );

        ///////////////////////////////////
        // INCREMENT COPYEDITOR ROTATION //
        ///////////////////////////////////

        if ( $which_copyeditor < count($copyeditor_email_list) - 1 ) {
            $which_copyeditor++;
            update_option( 'copyeditor_rotation', $which_copyeditor );
        } else {
            update_option( 'copyeditor_rotation', 0 );
        }

        wp_redirect( admin_url() );
        exit();

	}

}
add_action( 'draft_to_pending', 'draft_submitted_by_author' );


function draft_published_by_copyeditor() {

    global $post, $submission_editor_email;

    $submission_page = get_page_by_title( 'New Submission' );
    $parent_page = $post->post_parent;


    if ( $parent_page == $submission_page->ID ) {

        // SET REQUIRED VARIABLES
        $post_meta = get_post_custom( $post->ID );
        $user_info = get_userdata($post->post_author);
        $PR_email_1 = $post_meta['PR_email_1'][0];
        $PR_email_2 = $post_meta['PR_email_2'][0];

        ///////////////////////////
        // SEND EMAIL TO AUTHORS //
        ///////////////////////////

        /**
         * Set the recipients to an array of all the authors listed.
         * After, if the array value is empty, remove it.
         */
        $recipients = array(
            $user_info->user_email,
            $post_custom['coauthor_1_email'][0],
            $post_custom['coauthor_2_email'][0],
            $post_custom['coauthor_3_email'][0],
            $post_custom['coauthor_4_email'][0],
        );
        foreach($recipients as $key => $value) {
        	if($value == '' || $value === 'undefined') {
            	unset($recipients[$key]);
            }
		}

        // SET REQUIRED EMAIL VARIABLES
        $headers = array(
            'From: ALiEM Team <submission@aliem.com>',
            'Cc: ' . $submission_editor_email,
            'Content-Type: text/html',
            'charset=UTF-8',
        );
        $subject = "Pending Expert Peer Review: " . $post->post_title;
        $message = "<img src='http://aliem.com/wp-content/uploads/2013/05/logo-horizontal-color.png'><br>" .
                   "<div style='font-size: 18px;'>" .
                   "<p>Greetings!,</p>" .
                   "<p>This message is to inform you that your draft, <a href='" . $post->guid . "'>" . $post->post_title . "</a>, " .
                   "has cleared the copyedit stage and has been sent to your reviewer(s) for peer review.</p>" .
                   "<p>To receive notifications of any reviews or comments, please navigate to the " .
                   "published draft page using the link above and 'star' the Disqus feed below the draft. " .
                   "All further communication will take place via this comment feed.</p>" .
                   "<p>Thank you again for your hard work! We look forward to working with you again in the future.</p>" .
                   "<p>Kind regards,<br>The ALiEM Team</p>";
        wp_mail( $recipients, $subject, $message, $headers );

        /////////////////////////////
        // SEND EMAIL TO REVIEWERS //
        /////////////////////////////

        if ($PR_email_1 !== '') {

    		$subject = 'ALiEM Expert Peer Review Request';
    		$message = "<img src='http://aliem.com/wp-content/uploads/2013/05/logo-horizontal-color.png'><br>" .
                        "<div style='font-size: 18px;'><p>Greetings! It is our understanding that " . $user_info->first_name . " " .
                        $user_info->last_name . " spoke with you about being the Expert Peer Reviewer " .
                        "for his/her guest submission to ALiEM. We are very grateful for your participation.</p>" .
                        "<p>The draft for review can be found here: <a href='" . $post->guid . "'>" . $post->post_title . "</a>.</p>" .
                        "Please provide your peer review comments in the Disqus feed below the blog post. " .
                        "<p>Because we intend on appending your review to the published post, please provide your comments " .
                        "in a polished, academic format.</p>" .
                        "<p>If you run into any trouble or would like further clarification regarding our submission process, " .
                        "please feel free to contact our Submission Editor at any time. You'll find his email address cc'ed to this message.</p>" .
                        "<p>We look forward to your input! Thank you again for your time.</p>" .
                        "<p>Kind regards,<br>" .
                        "The ALiEM Team</p></div>";

            wp_mail( $PR_email_1, $subject, $message, $headers );

        }

        if ($PR_email_2 !== '') {

            wp_mail( $PR_email_2, $subject, $message, $headers );

        }

    }
}
add_action( 'pending_to_publish', 'draft_published_by_copyeditor' );

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

// SHOW PEER REVIEWER METABOX ON POSTS THAT HAVE THE META
function display_peer_reviewer_meta( $post ) {

    $post_meta = get_post_custom( $post->ID );
    $PR1_first_name = $post_meta['PR_first_name_1'][0];

    if ( $PR1_first_name !== '' ) {
        add_meta_box( 'peer_reviewer_meta_box', 'Expert Peer Reviewer Information', 'add_peer_reviewer_meta_box', 'page', 'side', 'high' );
    }

}
add_action( 'admin_head', 'display_peer_reviewer_meta' );


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
