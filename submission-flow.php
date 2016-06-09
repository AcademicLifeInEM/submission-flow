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
 * NOTE: THIS PLUGIN DEPENDS ON THE MODIFICATION OF THE FOLLOWING PLUGINS:
 *
 * 1) CO-AUTHORS PLUS:
 *    - COMMENT OUT LINES 1130-1139. (version: 3.2.1) (starts with comment below)
 *    "// Allow users to always filter out certain users if needed (e.g. administrators)"
 * 2) FANCIEST AUTHOR BOX: (/includes/ts-fab-user-settings.php)
 *    - LINE 21: Comment out whole line
 *    - LINE 137: Comment out whole line ('<?php } // end if ?>')
 */

////////////////////
// PLUGIN GLOBALS //
////////////////////

/**
 * ADD COPYEDITORS' EMAIL ADDRESSES TO THE BELOW LIST
 */

// DEBUG VARIABLE
// $copyeditor_emails = array(
//     [
//         'name' => 'Derek Sifford',
//         'email' => 'dereksifford@gmail.com',
//         'slack' => 'U0976DHT4'
//     ]
// );

$SE_email = 'submission@aliem.com';
$copyeditor_emails = array(
                            [
                                'name'  => 'Dr. Michelle Lin',
                                'email' => 'mlin@aliem.com',
                                'slack' => 'U0975Q2L9',
                            ],
                            [
                                'name'  => 'Dr. Bryan Hayes',
                                'email' => 'bryanhayes13@gmail.com',
                                'slack' => 'U0977REBC',
                            ],
                            [
                                'name'  => 'Dr. Teresa Chan',
                                'email' => 'teresamchan@gmail.com',
                                'slack' => 'U09BA9NBC',
                            ],
                            [
                                'name'  => 'Dr. Nikita Joshi',
                                'email' => 'njoshi@aliem.com',
                                'slack' => 'U097PKBB7',
                            ],
                            [
                                'name' => 'Dr. Fareen Zaver',
                                'email' => 'Fzaver@gmail.com',
                                'slack' => 'U097RKY2X',
                            ]
                        );

// Enqueue Javascript
function enqueue_plugin_scripts() {

    wp_enqueue_style( 'submission-flow-css', plugins_url( 'inc/submission-flow.css', __FILE__ ) );

    if ( current_user_can( 'subscriber' ) ) {

        // Enqueue javascript to hide profile items
        $the_current_url = "http://" . $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI'];
        if ( $the_current_url == get_edit_profile_url() ) {
            wp_enqueue_script('profile-hide', plugins_url( 'inc/js/profile-hide.js', __FILE__ ), array( 'jquery' ) );
        }

        /** Register scripts */
        wp_enqueue_script('dashboard-hide', plugins_url( 'inc/js/dashboard-hide.js', __FILE__ ), array( 'jquery' ) );
        wp_enqueue_script('form-handler', plugins_url( 'inc/js/form-handling.js', __FILE__ ), array( 'jquery' ) );

        /** Register, localize, and enqueue media upload button scripts */
        wp_register_script('media-uploads', plugins_url( 'inc/js/media-uploads.js', __FILE__ ), array( 'jquery' ) );
        wp_localize_script( 'media-uploads', 'meta_image',
            array(
                'title' => 'Choose or Upload an Image',
                'button' => 'Use this image',
            )
        );
        wp_enqueue_script( 'media-uploads' );
        return;

    }

    global $post;

    $submission_page = get_page_by_title( 'New Submission' );
    $parent_page = $post->post_parent;

    if ( $parent_page == $submission_page->ID ) {
        wp_enqueue_script( 'copyeditor-views', plugins_url( 'inc/js/copyeditor-views.js', __FILE__ ), array( 'jquery' ) );
    }

}
add_action( 'admin_enqueue_scripts', 'enqueue_plugin_scripts' );


function enqueue_plugin_frontend_scripts() {
    if (current_user_can( 'subscriber' ))
        wp_enqueue_script('frontend-hide', plugins_url( 'inc/js/frontend-hide.js', __FILE__ ), array( 'jquery' ) );
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


function call_submission_flow() {

    // TODO: Add current_user_can thing here
    new SubmissionFlow();
}

if (is_admin()) {
    add_action('load-post.php', 'call_submission_flow');
    add_action('load-post-new.php', 'call_submission_flow');
}


class SubmissionFlow {

    public function __construct() {
        add_action('add_meta_boxes', array($this, 'addMetaboxes'));
        add_action('save_post', array($this, 'saveMeta'));
    }

    public function addMetaboxes($postType) {
        if ( $postType === 'page' ) {
            add_meta_box(
                'peer_reviewer_meta_box',
                'Expert Peer Reviewer Information',
                array($this, 'peerReviewerBox'),
                'page',
                'side',
                'high'
            );
            add_meta_box(
                'coauthor_meta_box',
                'Co-author Details',
                array($this, 'coauthorBox'),
                'page',
                'advanced',
                'high'
            );
        }
    }

    public function peerReviewerBox($post) {
        wp_nonce_field( 'submission-flow-metaboxes', 'submission-flow-nonce' );

        $meta = json_decode(get_post_meta($post->ID, 'submission-flow', true), true);

        for ( $i = 1; $i < 3; $i++ ) {

            ${'PR_first_name_' . $i} = $meta['peer-reviewers']['PR_first_name_' . $i];
            ${'PR_last_name_' . $i} = $meta['peer-reviewers']['PR_last_name_' . $i];
            ${'PR_email_' . $i} = $meta['peer-reviewers']['PR_email_' . $i];
            ${'PR_twitter_handle_' . $i} = $meta['peer-reviewers']['PR_twitter_handle_' . $i];
            ${'PR_credentials_' . $i} = $meta['peer-reviewers']['PR_credentials_' . $i];
            ${'SF_photo_' . $i . '_url'} = $meta['peer-reviewers']['SF_photo_' . $i . '_url'];

        }

        require( 'inc/meta-peer-reviewer-info.php' );
    }

    public function coauthorBox($post) {
        $meta = json_decode(get_post_meta($post->ID, 'submission-flow', true), true);

        for ( $i = 1; $i < 5; $i++ ) {
            ${'coauthor_' . $i . '_first_name'} = $meta['coauthors']['coauthor_' . $i . '_first_name'];
            ${'coauthor_' . $i . '_last_name'} = $meta['coauthors']['coauthor_' . $i . '_last_name'];
            ${'coauthor_' . $i . '_email'} = $meta['coauthors']['coauthor_' . $i . '_email'];
            ${'coauthor_' . $i . '_twitter'} = $meta['coauthors']['coauthor_' . $i . '_twitter'];
            ${'coauthor_' . $i . '_credentials'} = $meta['coauthors']['coauthor_' . $i . '_credentials'];
        }

        for ( $i = 3; $i < 7; $i++ ) {
            ${'SF_photo_' . $i . '_url'} = $meta['coauthors']['SF_photo_' . $i . '_url'];
        }

        require( 'inc/meta-coauthors.php' );
    }

    public function saveMeta($postId) {

        $is_autosave = wp_is_post_autosave( $post_id );
    	$is_revision = wp_is_post_revision( $post_id );
    	$is_valid_nonce = (
            isset($_POST[ 'submission-flow-nonce']) &&
            wp_verify_nonce($_POST['submission-flow-nonce'], 'submission-flow-metaboxes')
        ) ? true : false;

        if ( $is_autosave || $is_revision || !$is_valid_nonce ) return;

        $meta = array(
            'peer-reviewers' => array(
                'PR_first_name_1'     => isset($_POST['PR_first_name_1']) ? $_POST['PR_first_name_1'] : '',
                'PR_last_name_1'      => isset($_POST['PR_last_name_1']) ? $_POST['PR_last_name_1'] : '',
                'PR_email_1'          => isset($_POST['PR_email_1']) ? $_POST['PR_email_1'] : '',
                'PR_credentials_1'    => isset($_POST['PR_credentials_1']) ? $_POST['PR_credentials_1'] : '',
                'SF_photo_1_url'      => isset($_POST['SF_photo_1_url']) ? $_POST['SF_photo_1_url'] : '',
                'PR_twitter_handle_1' => isset($_POST['PR_twitter_handle_1']) ? $_POST['PR_twitter_handle_1'] : '',
                'PR_first_name_2'     => isset($_POST['PR_first_name_2']) ? $_POST['PR_first_name_2'] : '',
                'PR_last_name_2'      => isset($_POST['PR_last_name_2']) ? $_POST['PR_last_name_2'] : '',
                'PR_email_2'          => isset($_POST['PR_email_2']) ? $_POST['PR_email_2'] : '',
                'PR_credentials_2'    => isset($_POST['PR_credentials_2']) ? $_POST['PR_credentials_2'] : '',
                'SF_photo_2_url'      => isset($_POST['SF_photo_2_url']) ? $_POST['SF_photo_2_url'] : '',
                'PR_twitter_handle_2' => isset($_POST['PR_twitter_handle_2']) ? $_POST['PR_twitter_handle_2'] : '',
            ),
            'coauthors' => array(
                'coauthor_1_first_name'  => isset($_POST['coauthor_1_first_name']) ? $_POST['coauthor_1_first_name'] : '',
                'coauthor_2_first_name'  => isset($_POST['coauthor_2_first_name']) ? $_POST['coauthor_2_first_name'] : '',
                'coauthor_3_first_name'  => isset($_POST['coauthor_3_first_name']) ? $_POST['coauthor_3_first_name'] : '',
                'coauthor_4_first_name'  => isset($_POST['coauthor_4_first_name']) ? $_POST['coauthor_4_first_name'] : '',
                'coauthor_1_last_name'   => isset($_POST['coauthor_1_last_name']) ? $_POST['coauthor_1_last_name'] : '',
                'coauthor_2_last_name'   => isset($_POST['coauthor_2_last_name']) ? $_POST['coauthor_2_last_name'] : '',
                'coauthor_3_last_name'   => isset($_POST['coauthor_3_last_name']) ? $_POST['coauthor_3_last_name'] : '',
                'coauthor_4_last_name'   => isset($_POST['coauthor_4_last_name']) ? $_POST['coauthor_4_last_name'] : '',
                'coauthor_1_email'       => isset($_POST['coauthor_1_email']) ? $_POST['coauthor_1_email'] : '',
                'coauthor_2_email'       => isset($_POST['coauthor_2_email']) ? $_POST['coauthor_2_email'] : '',
                'coauthor_3_email'       => isset($_POST['coauthor_3_email']) ? $_POST['coauthor_3_email'] : '',
                'coauthor_4_email'       => isset($_POST['coauthor_4_email']) ? $_POST['coauthor_4_email'] : '',
                'coauthor_1_twitter'     => isset($_POST['coauthor_1_twitter']) ? $_POST['coauthor_1_twitter'] : '',
                'coauthor_2_twitter'     => isset($_POST['coauthor_2_twitter']) ? $_POST['coauthor_2_twitter'] : '',
                'coauthor_3_twitter'     => isset($_POST['coauthor_3_twitter']) ? $_POST['coauthor_3_twitter'] : '',
                'coauthor_4_twitter'     => isset($_POST['coauthor_4_twitter']) ? $_POST['coauthor_4_twitter'] : '',
                'coauthor_1_credentials' => isset($_POST['coauthor_1_credentials']) ? wpautop($_POST['coauthor_1_credentials']) : '',
                'coauthor_2_credentials' => isset($_POST['coauthor_2_credentials']) ? wpautop($_POST['coauthor_2_credentials']) : '',
                'coauthor_3_credentials' => isset($_POST['coauthor_3_credentials']) ? wpautop($_POST['coauthor_3_credentials']) : '',
                'coauthor_4_credentials' => isset($_POST['coauthor_4_credentials']) ? wpautop($_POST['coauthor_4_credentials']) : '',
                'SF_photo_3_url'         => isset($_POST['SF_photo_3_url']) ? urlencode($_POST['SF_photo_3_url']) : '',
                'SF_photo_4_url'         => isset($_POST['SF_photo_4_url']) ? urlencode($_POST['SF_photo_4_url']) : '',
                'SF_photo_5_url'         => isset($_POST['SF_photo_5_url']) ? urlencode($_POST['SF_photo_5_url']) : '',
                'SF_photo_6_url'         => isset($_POST['SF_photo_6_url']) ? urlencode($_POST['SF_photo_6_url']) : '',
            )
        );

        update_post_meta($postId, 'submission-flow', json_encode($meta));


        for ($i = 1; $i < 5; $i++) {
            if (empty($_POST['coauthor_' . $i . '_first_name'])) continue;

            $firstname =  ucwords($_POST['coauthor_' . $i . '_first_name']);
            $lastname = ucwords($_POST['coauthor_' . $i . '_last_name']);
            $username = $firstname  . '.' . preg_replace('/\s+/', '.', $lastname);
            $email = strtolower($_POST['coauthor_' . $i . '_email']);
            $creds = $_POST['coauthor_' . $i . '_credentials'];
            $twitter = $_POST['coauthor_' . $i . '_twitter'];
            $photo = $_POST['SF_photo_' . ($i + 2) . '_url'];

            if ( get_user_by( 'email', $email ) === '' ) {
                $userdata = array(
                    'user_pass'    => 'ALiEMSubmissionUser',
                    'user_login'   => $username,
                    'user_email'   => $email,
                    'display_name' => $firstname . ' ' . $lastname,
                    'first_name'   => $firstname,
                    'last_name'    => $lastname,
                    'description'  => $creds,
                    'role'         => 'subscriber',
                );
                $new_user_id = wp_insert_user($userdata);
                update_user_meta($new_user_id, 'ts_fab_twitter', $twitter);
                update_user_meta($new_user_id, 'ts_fab_photo_url', $photo);
                continue;
            }

            $the_existing_user = get_user_by('email', $email);
            $userdata = array(
                'ID' => $the_existing_user->ID,
                'user_login' => $username,
                'description' => $creds,
            );
            wp_update_user($userdata);
            update_user_meta($the_existing_user->ID, 'ts_fab_twitter', $twitter);
            update_user_meta($the_existing_user->ID, 'ts_fab_photo_url', $photo);
        }
    }
}


// FIXME: current_user_can thing
// // DISPLAY META FOR COPYEDITORS
// function display_meta_for_copyeditors() {
//
//     global $post;
//
//     $submission_page = get_page_by_title( 'New Submission' );
//     $parent_page = $post->post_parent;
//
//
//     if ( $parent_page == $submission_page->ID ) {
//
//         $post_meta = get_post_custom( $post->ID );
//
//         for ( $i = 1; $i < 3; $i++ ) {
//
//             ${'PR_first_name_' . $i} = $post_meta['PR_first_name_' . $i][0];
//             ${'PR_last_name_' . $i} = $post_meta['PR_last_name_' . $i][0];
//             ${'PR_email_' . $i} = $post_meta['PR_email_' . $i][0];
//             ${'PR_twitter_handle_' . $i} = $post_meta['PR_twitter_handle_' . $i][0];
//             ${'PR_credentials_' . $i} = $post_meta['PR_credentials_' . $i][0];
//
//         }
//
//         for ( $i = 1; $i < 5; $i++ ) {
//
//             ${'coauthor_' . $i . '_first_name'} = $post_meta['coauthor_' . $i . '_first_name'][0];
//             ${'coauthor_' . $i . '_last_name'} = $post_meta['coauthor_' . $i . '_last_name'][0];
//             ${'coauthor_' . $i . '_email'} = $post_meta['coauthor_' . $i . '_email'][0];
//             ${'coauthor_' . $i . '_twitter'} = $post_meta['coauthor_' . $i . '_twitter'][0];
//             ${'coauthor_' . $i . '_credentials'} = $post_meta['coauthor_' . $i . '_credentials'][0];
//
//
//         }
//
//         for ( $i = 3; $i < 7; $i++ ) {
//             ${'SF_photo_' . $i . '_url'} = $post_meta['SF_photo_' . $i . '_url'][0];
//         }
//
//         add_meta_box( 'peer_reviewer_meta_box', 'Expert Peer Reviewer Information', 'add_peer_reviewer_meta_box', 'page', 'side', 'high' );
//         add_meta_box( 'coauthor_meta_box', 'Co-author Details', 'add_coauthor_meta_box', 'page', 'advanced', 'high');
//     }
//
// }
// add_action( 'add_meta_boxes', 'display_meta_for_copyeditors' );

/**
 * END META BOX FUNCTIONS
 */


/**
 * BEGIN EMAIL FUNCTIONS
 */

function draft_submitted_by_author($post) {

    if (current_user_can('subscriber')) {

        global $copyeditor_emails, $SE_email;
        $meta = json_decode(get_post_meta($post->ID, 'submission-flow', true), true);

        // SET PARENT PAGE
        $submission_page = get_page_by_title('New Submission');

        // ADD TITLE AND DRAFT IMAGE TO TOP OF DRAFT
        $updated_content = '<img class="aligncenter size-full wp-image-16521" src="http://www.aliem.com/wp-content/uploads/Draft.jpg" alt="Draft" width="400" height="116" /><h1>' . $post->post_title . '</h1>' . $post->post_content;
        $updated_post = array(
            'ID' => $post->ID,
            'post_parent' => $submission_page->ID,
            'comment_status' => 'open',
            'post_content' => $updated_content,
        );
        wp_update_post($updated_post);

        //////////////////////////////////
        // PREPARE VARIABLES FOR EMAILS //
        //////////////////////////////////

        // Is 'copyeditor_rotation' defined yet in the database? If not, start at 0
        if (!get_option('copyeditor_rotation')) add_option('copyeditor_rotation', 0);

        // Variable to hold current number in copyeditor rotation
        $which_copyeditor = get_option('copyeditor_rotation');

        // Get author details, then set display name for email
		$user_info = get_userdata($post->post_author);
        $submitter_name = ($user_info->display_name === '' ? $user_info->nicename : $user_info->display_name);

        /////////////////////////////////////////
        // PREPARE AND SEND CONFIRMATION EMAIL //
        /////////////////////////////////////////

        /**
         * Set the recipients to an array of all the authors listed.
         * After, if the array value is empty, remove it.
         */
        $recipients = array(
            $user_info->user_email,
            $meta['coauthors']['coauthor_1_email'],
            $meta['coauthors']['coauthor_2_email'],
            $meta['coauthors']['coauthor_3_email'],
            $meta['coauthors']['coauthor_4_email'],
        );
        foreach($recipients as $key => $value) {
        	if($value === '' || $value === 'undefined') {
            	unset($recipients[$key]);
            }
		}

        $headers = array(
            'From: ALiEM Team <submission@aliem.com>',
            'Cc: ' . $SE_email,
            'Content-Type: text/html',
            'charset=UTF-8',
        );
        $subject = 'Submission Received: "' . $post->post_title . '"';
        $message = "<img src='http://aliem.com/wp-content/uploads/2013/05/logo-horizontal-color.png'><br>" .
                   "<div style='font-size: 18px;'>" .
                   "<p>Thank you for your interest in posting content to ALiEM!</p>" .
                   "<p>Your copyeditor, " . $copyeditor_emails[$which_copyeditor]['name'] . ", has been notified and will begin " .
                   "proofing shortly. Once the proofing has been completed, you will be notified via email. " .
                   "At that time, we will also notify your selected Expert Peer Reviewer(s) that the draft is ready for their review. " .
                   "As a reminder, we assume that you have already spoken with your selected Expert Peer Reviewers and they have agreed to participate.</p>" .
                   "<p>If you have any questions, please feel free to contact your copyeditor or our Submission Editor via email at any time. " .
                   "For convienience, their contact information is listed below.</p>" .
                   "<ul><li><strong>Copyeditor</strong>: " . $copyeditor_emails[$which_copyeditor]['name'] . ", " . $copyeditor_emails[$which_copyeditor]['email'] . "</li>" .
                   "<li><strong>Submission Editor</strong>: Derek Sifford, submission@aliem.com</li></ul>" .
                   "<p>Thank you again for your interest. We look forward to working with you!</p>" .
                   "<p>Kind regards,<br>The ALiEM Team</p>";
        wp_mail( $recipients, $subject, $message, $headers );

        ///////////////////////////////////
        // INCREMENT COPYEDITOR ROTATION //
        ///////////////////////////////////

        if ( $which_copyeditor < count($copyeditor_emails) - 1 ) {
            $which_copyeditor++;
            update_option( 'copyeditor_rotation', $which_copyeditor );
            wp_redirect( admin_url() );
            return;
        }

        update_option( 'copyeditor_rotation', 0 );
        wp_redirect( admin_url() );

	}

}
add_action( 'draft_to_pending', 'draft_submitted_by_author' );


function draft_published_by_copyeditor() {

    global $post, $SE_email;

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
            'Cc: ' . $SE_email,
            'Content-Type: text/html',
            'charset=UTF-8',
        );
        $subject = "Pending Expert Peer Review: " . $post->post_title;
        $message = "<img src='http://aliem.com/wp-content/uploads/2013/05/logo-horizontal-color.png'><br>" .
                   "<div style='font-size: 18px;'>" .
                   "<p>Greetings!,</p>" .
                   "<p>This message is to inform you that your draft, <a href='" . $post->guid . "'>" . $post->post_title . "</a>, " .
                   "has cleared the copyedit stage and has been sent to your reviewer(s) for peer review.</p>" .
                   "<p>At your convienience, please address any questions or concerns from the Copyeditor. As a reminder, " .
                   "you still have full edit-access to the page. We encourage you to make any corrections or adjustments that you deem necessary.</p>" .
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

        // COLLECT PEER REVIEWER INFO AND THEN SET IT AS PEER REVIEWER BOX POSTMETA
        $post_meta = get_post_custom( $post->ID );

        // COLLECT LIST OF COMMENTS FOR THE SUBMISSION DRAFT
        $comment_string = '****** Don\'t forget to cut and paste the reviews/comments below to the appropriate places! ****** <br><br><br> ';
        $comments = get_comments( array( 'post_id' => $post->ID ) );
        foreach ($comments as $comment) {
            $comment_string .= '----------------------------' . $comment->comment_author . ' (' . $comment->comment_date . ')----------------------------<br>' .
                                $comment->comment_content .
                                '<br>----------------------------END COMMENT----------------------------<br><br>';
        };

        for ( $i = 1; $i < 3; $i++ ) {

            ${'PR_first_name_' . $i} = $post_meta['PR_first_name_' . $i][0];
            ${'PR_last_name_' . $i} = $post_meta['PR_last_name_' . $i][0];
            ${'PR_email_' . $i} = $post_meta['PR_email_' . $i][0];
            ${'PR_twitter_handle_' . $i} = $post_meta['PR_twitter_handle_' . $i][0];
            ${'PR_credentials_' . $i} = $post_meta['PR_credentials_' . $i][0];
            ${'SF_photo_' . $i . '_url'} = $post_meta['SF_photo_' . $i . '_url'][0];

        }

        // UPDATE PEER REVIEWER BOX META VALUES FOR POST
        update_post_meta( $post->ID, 'peer_review_box_heading_1', 'ALiEM Copyedit' );
        update_post_meta( $post->ID, 'peer_review_content_1', $comment_string );
        update_post_meta( $post->ID, 'peer_review_box_heading_2', 'Expert Peer Review' );
        update_post_meta( $post->ID, 'reviewer_name_2', $PR_first_name_1 . ' ' . $PR_last_name_1 );
        update_post_meta( $post->ID, 'reviewer_twitter_2', $PR_twitter_handle_1 );
        update_post_meta( $post->ID, 'reviewer_background_2', $PR_credentials_1 );
        update_post_meta( $post->ID, 'reviewer_selector', '2' );
        update_post_meta( $post->ID, 'reviewer_headshot_image_2', $SF_photo_1_url );

        if ( $PR_first_name_2 !== '' ) {

            update_post_meta( $post->ID, 'peer_review_box_heading_3', 'Expert Peer Review' );
            update_post_meta( $post->ID, 'reviewer_name_3', $PR_first_name_2 . ' ' . $PR_last_name_2 );
            update_post_meta( $post->ID, 'reviewer_twitter_3', $PR_twitter_handle_2 );
            update_post_meta( $post->ID, 'reviewer_background_3', $PR_credentials_2 );
            update_post_meta( $post->ID, 'reviewer_selector', '3' );
            update_post_meta( $post->ID, 'reviewer_headshot_image_3', $SF_photo_2_url );

        }

        $page_to_post = $post;
        $page_to_post->post_type = 'post';
        wp_update_post( $page_to_post );
    }

}
add_action('publish_to_draft', 'finalize_submission');



/**
 * reCAPTCHA REGISTRATION INTEGRATION
 */

 // reCAPTCHA HEADER SCRIPT
 function header_script() {
 echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>' .
 '<style type="text/css">'.
     '#login {width: 350px !important;}' .
     '.login h1 a {background-image: url(http://aliem.com/wp-content/uploads/2013/05/logo-horizontal-color.png);' .
                 ' background-size: auto; width: auto; height: auto; font-size: 40px; margin: 0 auto 10px;}' .
     '.login form {padding: 26px 24px;}' .
     '.login #nav {text-align: center;}' .
     '.login #backtoblog {text-align: center;}' .
 '</style>';
 }
 add_action( 'wp_head', 'header_script' );
 add_action( 'login_enqueue_scripts', 'header_script' );

 // VERIFY CAPTCHA
 function captcha_verification() {

     $secret = '6Ld5GAsTAAAAANhwA05axeB92KjWkVrE_4qrX-mT';
     $response = isset( $_POST['g-recaptcha-response'] ) ? esc_attr( $_POST['g-recaptcha-response'] ) : '';
     $remote_ip = $_SERVER["REMOTE_ADDR"];

     $post_body = array(
         'secret' => $secret,
         'reponse' => $response,
         'remoteip' => $remote_ip,
     );

     $args = array( 'body' => $post_body );

     // make a GET request to the Google reCAPTCHA Server
     $request = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', $args );
     $response_body = wp_remote_retrieve_body( $request );

     $answers = explode( "\n", $response_body );
     $request_status = trim( $answers[0] );
     return $request_status;

     // $result = json_decode( $response_body, true );
     // return $result['success'];
 }

  // DISPLAY reCAPTCHA ON REGISTRATION FORM
 function display_captcha() {
      echo '<div class="g-recaptcha" data-sitekey="6Ld5GAsTAAAAANTxfQ1U9BMm2b7o0pl-6OPoa4U3"></div>' .
      '<noscript>
  <div style="width: 302px; height: 422px;">
    <div style="width: 302px; height: 422px; position: relative;">
      <div style="width: 302px; height: 422px; position: absolute;">
        <iframe src="https://www.google.com/recaptcha/api/fallback?k=6Ld5GAsTAAAAANTxfQ1U9BMm2b7o0pl-6OPoa4U3"
                frameborder="0" scrolling="no"
                style="width: 302px; height:422px; border-style: none;">
        </iframe>
      </div>
      <div style="width: 300px; height: 60px; border-style: none;
                  bottom: 12px; left: 25px; margin: 0px; padding: 0px; right: 25px;
                  background: #f9f9f9; border: 1px solid #c1c1c1; border-radius: 3px;">
        <textarea id="g-recaptcha-response" name="g-recaptcha-response"
                  class="g-recaptcha-response"
                  style="width: 250px; height: 40px; border: 1px solid #c1c1c1;
                         margin: 10px 25px; padding: 0px; resize: none;" >
        </textarea>
      </div>
    </div>
  </div>
</noscript>';
  }
 add_action( 'register_form', 'display_captcha' );

  // AUTHENTICATE reCAPTCHA
 function validate_captcha_registration_field( $errors, $sanitized_user_login, $user_email ) {
      if ( isset( $_POST['g-recaptcha-response'] ) && !captcha_verification() ) {
          $errors->add( 'failed_verification', '<strong>ERROR</strong>: Please retry CAPTCHA' );
      }

      return $errors;
  }
 add_action( 'registration_errors', 'validate_captcha_registration_field', 10, 3 );

/**
 * END reCAPTCHA INTEGRATION
 */



/**
 * HELPER FUNCTIONS
 */
function parse_html_chars( $input ) {
    $output = preg_replace( "/<p>/", "", $input );
    $output = preg_replace( "/<br \\/>/", "\r", $output );
    $output = preg_replace( "/<\\/p>/", "\n", $output );
    return( trim( $output ) );
}

?>
