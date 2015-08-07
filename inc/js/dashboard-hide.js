jQuery(document).ready(function($) {

    /**
     * HIDE EVERYTHING BY DEFAULT (explicit shows following)
     */
    $('#wp-admin-bar-new-media').hide();
    $('#adminmenu > li').hide(); // Admin Menu (sidebar) Links
    $('#normal-sortables').children().hide(); // First set of sortables
    $('#dashboard_primary').hide(); // Second set of sortables
    $('#postbox-container-3').hide();
    $('#postbox-container-4').hide();
    $('#screen-options-link-wrap').hide();
    $('#postimagediv').hide();
    $('#latest-comments').hide();
    $('#add_poll').hide();
    $('#wp-admin-bar-ab-ls-add-new').hide();
    $('#fusion-pb-switch-button').hide();
    $('#pyre_page_options').hide();
    $('#pageparentdiv').hide();
    $('#featured-image-2_page').hide();
    $('#featured-image-3_page').hide();
    $('#featured-image-4_page').hide();
    $('#featured-image-5_page').hide();
    $('.misc-pub-section misc-yoast').hide();
    $('.misc-pub-section misc-pub-visibility').hide();

    /**
     * EXPLICIT SHOWS
     */
    // ADMIN BAR LINKS
    $('.wp-admin-bar-wp-logo').show();
    $('.wp-admin-bar-site-name').show();


    // SIDEBAR LINKS
    $('#menu-dashboard').show();
    $('#menu-pages').show();
    $('#menu-users').show();

    // DASHBOARD ITEMS
    $('#dashboard_right_now').show();
    $('#dashboard_activity').show();

    // META BOXES
    $('#peer_reviewer_info_metabox').show();
    $('#coauthor_meta_box').show();

});
