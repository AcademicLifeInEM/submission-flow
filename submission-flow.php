<?php

/*
 *	Plugin Name: ALiEM Submission Flow
 *	Plugin URI: null
 *	Description: Internal plugin to enhance new submission flow
 *	Version: 1.0.0
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

/**
 * ADD COPYEDITORS' EMAIL ADDRESSES TO THE BELOW LIST
 */

// DEBUG VARIABLE
// $copyeditors = array(
//     [
//         'name' => 'Derek Sifford',
//         'email' => 'dereksifford@gmail.com',
//         'slack' => 'U0976DHT4'
//     ]
// );

$copyeditors = array(
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
    ],
);


/**
 * @SuppressWarnings(PHPMD.Superglobals)
 */
function enqueue_plugin_scripts() {

    wp_enqueue_style('submission-flow-css', plugins_url('inc/submission-flow.css', __FILE__ ) );

    if (current_user_can('subscriber')) {
        // Enqueue javascript to hide profile items
        $the_current_url = "http://" . $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI'];
        if ($the_current_url == get_edit_profile_url()) {
            wp_enqueue_script('profile-hide', plugins_url('inc/js/profile-hide.js', __FILE__), array('jquery'));
        }
        wp_enqueue_script('dashboard-hide', plugins_url('inc/js/dashboard-hide.js', __FILE__), array('jquery'));
        wp_enqueue_script('form-handler', plugins_url('inc/js/form-handling.js', __FILE__), array('jquery'));
        wp_enqueue_script('media-uploads', plugins_url('inc/js/media-uploads.js', __FILE__), array('jquery'));
        return;
    }

    global $post;

    if (!$post) return;

    $submissionPage = get_page_by_title('New Submission');
    $parentPage = $post->post_parent;

    if ($parentPage == $submissionPage->ID) {
        wp_enqueue_script('copyeditor-views', plugins_url('inc/js/copyeditor-views.js', __FILE__), array('jquery'));
    }

}
add_action('admin_enqueue_scripts', 'enqueue_plugin_scripts');


function enqueue_plugin_frontend_scripts() {
    if (current_user_can('subscriber'))
        wp_enqueue_script('frontend-hide', plugins_url('inc/js/frontend-hide.js', __FILE__), array('jquery'));
}
add_action('wp_enqueue_scripts', 'enqueue_plugin_frontend_scripts');


// Customize capabilities for subscribers
function adjust_subscriber_capabilities() {
    $role = get_role('subscriber');
    $role->add_cap('edit_pages');
    $role->add_cap('upload_files');
    $role->add_cap('edit_published_pages');
}
add_action('admin_init', 'adjust_subscriber_capabilities');


/**
 * Only allow subscribers to see their own media uploads
 * @SuppressWarnings(PHPMD.Superglobals, PHPMD.CamelCaseVariableName)
 */
function users_own_attachments($query) {
    global $current_user, $pagenow;

    if(!is_a($current_user, 'WP_User'))
        return;

    if(('upload.php' != $pagenow) && (('admin-ajax.php' != $pagenow) || ($_REQUEST['action'] != 'query-attachments')))
        return;

    if(current_user_can('subscriber'))
        $query->set('author', $current_user->id);

    return;
}
add_action('pre_get_posts','users_own_attachments');



function call_submission_flow() {
    new SubmissionFlow();
}

if (is_admin()) {
    add_action('load-post.php', 'call_submission_flow');
    add_action('load-post-new.php', 'call_submission_flow');
}

/**
 * @SuppressWarnings("complexity")
 */
class SubmissionFlow {

    public function __construct() {
        add_action('add_meta_boxes', array($this, 'addMetaboxes'));
        add_action('save_post', array($this, 'saveMeta'));
    }

    public function addMetaboxes($postType) {
        if ($postType !== 'page') return;

        global $post;
        $parent = get_page_by_title('New Submission')->ID;

        if (!current_user_can('subscriber') && $post->post_parent != $parent) return;

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

    /**
     * Function to send HTTP POST data to the slackbot server
     * @param  string $endpoint The endpoint to hit.
     * @param  array $body      Associative array of data.
     * @return void
     */
    public static function slackMsg($endpoint, $body) {
        $key = get_option('ALIEM_API_KEY');
        for ($i = 0; $i < 5; $i++) {
            $response = wp_remote_post(
                "http://104.131.189.237:5000/newsubmissions/hooks/$endpoint",
                array(
                    'headers' => array(
                        'ALIEM_API_KEY' => $key,
                    ),
                    'body' => $body
                )
            );
            if (!is_wp_error($response) && $response['response']['code'] == 200) {
                break;
            }
        }
    }

    public function peerReviewerBox($post) {
        wp_nonce_field('submission-flow-metaboxes', 'submission-flow-nonce');

        $meta = json_decode(get_post_meta($post->ID, 'submission-flow', true), true);

        for ($i = 1; $i < 3; $i++) {
            ${'PR_first_name_' . $i} = $meta['peer-reviewers']['PR_first_name_' . $i];
            ${'PR_last_name_' . $i} = $meta['peer-reviewers']['PR_last_name_' . $i];
            ${'PR_email_' . $i} = $meta['peer-reviewers']['PR_email_' . $i];
            ${'PR_twitter_handle_' . $i} = $meta['peer-reviewers']['PR_twitter_handle_' . $i];
            ${'PR_credentials_' . $i} = $meta['peer-reviewers']['PR_credentials_' . $i];
            ${'SF_photo_' . $i . '_url'} = $meta['peer-reviewers']['SF_photo_' . $i . '_url'];
        }

        require('inc/meta-peer-reviewer-info.php');
    }

    public function coauthorBox($post) {
        $meta = json_decode(get_post_meta($post->ID, 'submission-flow', true), true);

        for ($i = 1; $i < 5; $i++) {
            ${'coauthor_' . $i . '_first_name'} = $meta['coauthors']['coauthor_' . $i . '_first_name'];
            ${'coauthor_' . $i . '_last_name'} = $meta['coauthors']['coauthor_' . $i . '_last_name'];
            ${'coauthor_' . $i . '_email'} = $meta['coauthors']['coauthor_' . $i . '_email'];
            ${'coauthor_' . $i . '_twitter'} = $meta['coauthors']['coauthor_' . $i . '_twitter'];
            ${'coauthor_' . $i . '_credentials'} = $meta['coauthors']['coauthor_' . $i . '_credentials'];
            ${'SF_photo_' . ($i + 2) . '_url'} = $meta['coauthors']['SF_photo_' . ($i + 2) . '_url'];
        }

        require('inc/meta-coauthors.php');
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity, PHPMD.NPathComplexity, PHPMD.Superglobals)
     */
    public function saveMeta($postId) {

        $isAutosave = wp_is_post_autosave($postId);
    	$isRevision = wp_is_post_revision($postId);
    	$validNonce = (
            isset($_POST[ 'submission-flow-nonce']) &&
            wp_verify_nonce($_POST['submission-flow-nonce'], 'submission-flow-metaboxes')
        ) ? true : false;

        if ($isAutosave || $isRevision || !$validNonce) return;

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

            if (!get_user_by('email', $email)) {
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
                $newUserId = wp_insert_user($userdata);
                update_user_meta($newUserId, 'ts_fab_twitter', $twitter);
                update_user_meta($newUserId, 'ts_fab_photo_url', $photo);
                continue;
            }

            $existingUser = get_user_by('email', $email);
            $userdata = array(
                'ID' => $existingUser->ID,
                'user_login' => $username,
                'description' => $creds,
            );
            wp_update_user($userdata);
            update_user_meta($existingUser->ID, 'ts_fab_twitter', $twitter);
            update_user_meta($existingUser->ID, 'ts_fab_photo_url', $photo);
        }
    }
}


 /**
  * @SuppressWarnings(PHPMD.ExitExpression)
  */
function author_submit($post) {
    global $copyeditors;
    if (!current_user_can('subscriber')) return;
    $meta = json_decode(get_post_meta($post->ID, 'submission-flow', true), true);

    if (empty($meta) || !$meta) {
        $meta = array(
            'coauthors' => array(
                'coauthor_1_email'       => isset($_POST['coauthor_1_email']) ? $_POST['coauthor_1_email'] : '',
                'coauthor_2_email'       => isset($_POST['coauthor_2_email']) ? $_POST['coauthor_2_email'] : '',
                'coauthor_3_email'       => isset($_POST['coauthor_3_email']) ? $_POST['coauthor_3_email'] : '',
                'coauthor_4_email'       => isset($_POST['coauthor_4_email']) ? $_POST['coauthor_4_email'] : '',
            )
        );
    }

    $submissionPage = get_page_by_title('New Submission');
    $rotation = get_option('copyeditor_rotation', 0);
    $submitter = get_userdata($post->post_author);
    $recipients = array(
        $submitter->user_email,
        $meta['coauthors']['coauthor_1_email'],
        $meta['coauthors']['coauthor_2_email'],
        $meta['coauthors']['coauthor_3_email'],
        $meta['coauthors']['coauthor_4_email'],
    );
    $recipients = array_filter($recipients);

    $headers = array(
        'From: ALiEM Team <submission@aliem.com>',
        'Content-Type: text/html',
        'charset=UTF-8',
    );
    $subject = "Submission Received: \"$post->post_title\"";
    $message = "
        <img src='https://aliem.com/wp-content/uploads/2013/05/logo-horizontal-color.png'><br>
        <div style='font-size: 18px;'>
            <p>
                Thank you for your interest in posting content to ALiEM!
            </p>
            <p>
                Your copyeditor, {$copyeditors[$rotation]['name']}, has been notified and will begin
                proofing shortly. Once the proofing has been completed, you will be notified via email.
                At that time, we will also notify your selected Expert Peer Reviewer(s) that the draft is ready for their review.
                As a reminder, we assume that you have already spoken with your selected Expert Peer Reviewers and they have agreed to participate.</p>
            <p>
                If you have any questions, please feel free to contact your copyeditor or our Submission Editor via email at any time.
                For convienience, their contact information is listed below.
            </p>
            <ul>
                <li>
                    <strong>Copyeditor</strong>: {$copyeditors[$rotation]['name']}, {$copyeditors[$rotation]['email']}
                </li>
                <li>
                    <strong>Submission Editor</strong>: Derek Sifford, submission@aliem.com
                </li>
            </ul>
            <p>
                Thank you again for your interest. We look forward to working with you!
            </p>
            <p>
                Kind regards,<br>
                The ALiEM Team
            </p>
        </div>";
    wp_mail($recipients, $subject, $message, $headers);

    SubmissionFlow::slackMsg('draft-submit', array(
        'title' => $post->post_title,
        'author' => $submitter->display_name,
        'copyeditor' => $copyeditors[$rotation]['slack'],
        'draftUrl' => $post->guid
    ));

    // ADD TITLE AND DRAFT IMAGE TO TOP OF DRAFT
    $updatedContent = '<img class="aligncenter size-full wp-image-16521" src="http://www.aliem.com/wp-content/uploads/Draft.jpg" alt="Draft" width="400" height="116" /><h1>' . $post->post_title . '</h1>' . $post->post_content;
    $updatedPost = array(
        'ID' => $post->ID,
        'post_parent' => $submissionPage->ID,
        'comment_status' => 'open',
        'post_content' => $updatedContent,
    );
    wp_update_post($updatedPost);

    // Increment copyedit rotation
    $rotation = $rotation < (count($copyeditors) - 1) ? ($rotation + 1) : 0;
    update_option('copyeditor_rotation', $rotation);
}
add_action('draft_to_pending', 'author_submit');
add_action('new_to_pending', 'author_submit');
add_action('auto-draft_to_pending', 'author_submit');



function copyeditor_submit($post) {
    $submissionPage = get_page_by_title('New Submission');
    $parentPage = $post->post_parent;

    if ($parentPage != $submissionPage->ID) return;

    $meta = json_decode(get_post_meta($post->ID, 'submission-flow', true), true);

    $userInfo = get_userdata($post->post_author);
    $recipients = array(
        $userInfo->user_email,
        $meta['coauthors']['coauthor_1_email'],
        $meta['coauthors']['coauthor_2_email'],
        $meta['coauthors']['coauthor_3_email'],
        $meta['coauthors']['coauthor_4_email'],
    );
    $recipients = array_filter($recipients);
    $email1 = $meta['peer-reviewers']['PR_email_1'][0];
    $email2 = $meta['peer-reviewers']['PR_email_2'][0];
    $headers = array(
        'From: ALiEM Team <submission@aliem.com>',
        'Content-Type: text/html',
        'charset=UTF-8',
    );

    // Author email
    $subject = "Pending Expert Peer Review: $post->post_title";
    $message = "
        <img src='http://aliem.com/wp-content/uploads/2013/05/logo-horizontal-color.png'>
        <br>
        <div style='font-size: 18px;'>
            <p>Greetings!,</p>
            <p>
                This message is to inform you that your draft, <a href='$post->guid'>$post->post_title</a>,
                has cleared the copyedit stage and has been sent to your reviewer(s) for peer review.
            </p>
            <p>
                At your convienience, please address any questions or concerns from the Copyeditor. As a reminder,
                you still have full edit-access to the page. We encourage you to make any corrections or adjustments that you deem necessary.
            </p>
            <p>
                To receive notifications of any reviews or comments, please navigate to the
                published draft page using the link above and 'star' the Disqus feed below the draft.
                All further communication will take place via this comment feed.</p>
            <p>
                Thank you again for your hard work! We look forward to working with you again in the future.
            </p>
            <p>
                Kind regards,<br>
                The ALiEM Team
            </p>
        </div>";
    wp_mail($recipients, $subject, $message, $headers);

    // Reviewer email
	$subject = 'ALiEM Expert Peer Review Request';
	$message = "
        <img src='http://aliem.com/wp-content/uploads/2013/05/logo-horizontal-color.png'>
        <br>
        <div style='font-size: 18px;'>
            <p>
                Greetings! It is our understanding that $userInfo->first_name
                $userInfo->last_name spoke with you about being the Expert Peer Reviewer
                for his/her guest submission to ALiEM. We are very grateful for your participation.
            </p>
            <p>
                The draft for review can be found here: <a href='$post->guid'>$post->post_title</a>.
            </p>
            <p>
                Please provide your peer review comments in the Disqus feed below the blog post.
            </p>
            <p>
                Because we intend on appending your review to the published post, please provide your comments
                in a polished, academic format.
            </p>
            <p>
                If you run into any trouble or would like further clarification regarding our submission process,
                please feel free to contact our Submission Editor at any time at <a href='mailto:submission@aliem.com'>submission@aliem.com</a>.
            </p>
            <p>
                We look forward to your input! Thank you again for your time.
            </p>
            <p>
                Kind regards,<br>
                The ALiEM Team
            </p>
        </div>";
    wp_mail($email1, $subject, $message, $headers);

    if ($email2 !== '')
        wp_mail($email2, $subject, $message, $headers);

    SubmissionFlow::slackMsg('copyeditor-submit', array(
        'title' => $post->post_title,
        'url' => $post->guid,
    ));
}
add_action('pending_to_publish', 'copyeditor_submit');


// Convert finalized submission 'page' into a 'post' on status change (publish to draft)
function finalize_submission($post) {

    $submissionPage = get_page_by_title('New Submission');
    $parentPage = $post->post_parent;

    if (!$parentPage || $parentPage != $submissionPage->ID) return;

    // Collect peer reviewer info and then set it as peer reviewer box postmeta
    $meta = json_decode(get_post_meta($post->ID, 'submission-flow', true), true);
    $fname1 = $meta['peer-reviewers']['PR_first_name_1'];
    $lname1 = $meta['peer-reviewers']['PR_last_name_1'];
    $twitter1 = $meta['peer-reviewers']['PR_twitter_handle_1'];
    $creds1 = $meta['peer-reviewers']['PR_credentials_1'];
    $image1 = $meta['peer-reviewers']['SF_photo_1_url'];
    $fname2 = $meta['peer-reviewers']['PR_first_name_2'];
    $lname2 = $meta['peer-reviewers']['PR_last_name_2'];
    $twitter2 = $meta['peer-reviewers']['PR_twitter_handle_2'];
    $creds2 = $meta['peer-reviewers']['PR_credentials_2'];
    $image2 = $meta['peer-reviewers']['SF_photo_2_url'];

    // Collect list of comments for the submission draft
    $commentString = "<br>";
    $comments = get_comments(array('post_id' => $post->ID));
    foreach ($comments as $comment) {
        $commentString .= "
            ------ $comment->comment_author ($comment->comment_date) ------<br>
            $comment->comment_content <br>
            ------ END COMMENT ------<br><br>";
    };

    $updatedContent = "
        <div>
            <h3>Comments</h3>
            $commentString
        </div>
        <div>
            <h3>Peer Reviewer 1 Info</h3>
            <ul>
                <li>Name: $fname1 $lname1</li>
                <li>Twitter: $twitter1</li>
                <li>Background: $creds1</li>
                <li>Image: $image1</li>
            </ul>
            <h3>Peer Reviewer 2 Info</h3>
            <ul>
                <li>Name: $fname2 $lname2</li>
                <li>Twitter: $twitter2</li>
                <li>Background: $creds2</li>
                <li>Image: $image2</li>
            </ul>
        </div>
    " . $post->post_content;

    $updatedPost = array(
        'ID' => $post->ID,
        'post_type' => 'post',
        'post_content' => $updatedContent,
    );
    wp_update_post($updatedPost);
    SubmissionFlow::slackMsg('finalize-submission', array(
        'title' => $post->post_title,
        'url' => $post->guid,
    ));
}
add_action('publish_to_draft', 'finalize_submission');



// reCAPTCHA HEADER SCRIPT
function header_script() { ?>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style type="text/css">
        #login {
            width: 350px !important;
        }
        .login h1 a {
            background-image: url('http://aliem.com/wp-content/uploads/2013/05/logo-horizontal-color.png');
            background-size: auto;
            width: auto;
            height: auto;
            font-size: 40px;
            margin: 0 auto 10px;
        }
        .login form {
            padding: 26px 24px;
        }
        .login #nav {
            text-align: center;
        }
        .login #backtoblog {
            text-align: center;
        }
    </style>
<?php }
add_action('wp_head', 'header_script');
add_action('login_enqueue_scripts', 'header_script');


/**
 * Verify reCAPTCHA
 * @SuppressWarnings(PHPMD.Superglobals)
 */
function captcha_verification() {
    $secret = '6Ld5GAsTAAAAANhwA05axeB92KjWkVrE_4qrX-mT';
    $response = isset($_POST['g-recaptcha-response']) ? esc_attr($_POST['g-recaptcha-response']) : '';
    $remoteIP = $_SERVER["REMOTE_ADDR"];

    $postBody = array(
        'secret' => $secret,
        'reponse' => $response,
        'remoteip' => $remoteIP,
    );

    $args = array('body' => $postBody);

    // make a GET request to the Google reCAPTCHA Server
    $request = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', $args);
    $responseBody = wp_remote_retrieve_body($request);

    $answers = explode("\n", $responseBody);
    $status = trim($answers[0]);
    return $status;
}

// Display reCAPTCHA on registration form
function display_captcha() { ?>
    <div class="g-recaptcha" data-sitekey="6Ld5GAsTAAAAANTxfQ1U9BMm2b7o0pl-6OPoa4U3"></div>
    <noscript>
        <div style="width: 302px; height: 422px;">
            <div style="width: 302px; height: 422px; position: relative;">
                <div style="width: 302px; height: 422px; position: absolute;">
                    <iframe src="https://www.google.com/recaptcha/api/fallback?k=6Ld5GAsTAAAAANTxfQ1U9BMm2b7o0pl-6OPoa4U3" frameborder="0" scrolling="no" style="width: 302px; height:422px; border-style: none;"></iframe>
                </div>
                <div style="width: 300px; height: 60px; border-style: none; bottom: 12px; left: 25px; margin: 0px; padding: 0px; right: 25px; background: #f9f9f9; border: 1px solid #c1c1c1; border-radius: 3px;">
                    <textarea id="g-recaptcha-response" name="g-recaptcha-response" class="g-recaptcha-response" style="width: 250px; height: 40px; border: 1px solid #c1c1c1; margin: 10px 25px; padding: 0px; resize: none;" ></textarea>
                </div>
            </div>
        </div>
    </noscript>
<?php }
add_action('register_form', 'display_captcha');


/**
 * Authenticate reCAPTCHA
 * @SuppressWarnings(PHPMD.Superglobals, PHPMD.CamelCaseParameterName, PHPMD.UnusedFormalParameter)
 */
function validate_captcha_registration_field($errors, $sanitized_user_login, $user_email) {
    if (isset($_POST['g-recaptcha-response']) && !captcha_verification())
        $errors->add('failed_verification', '<strong>ERROR</strong>: Please retry CAPTCHA');
    return $errors;
}
add_action('registration_errors', 'validate_captcha_registration_field', 10, 3);

/**
 * Helper functions
 */
function parse_html_chars($input) {
    $output = preg_replace("/<p>/", "", $input);
    $output = preg_replace("/<br \\/>n?/", "\r", $output);
    $output = preg_replace("/<\\/p>n?/", "\n\n", $output);
    return(trim($output));
}

?>
