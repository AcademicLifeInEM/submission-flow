jQuery(document).ready(function($) {

    /**
     * HIDE EVERYTHING BY DEFAULT (explicit shows following)
     */
    $('#wp-admin-bar-root-default > li').hide(); // Admin Bar Links
    $('#adminmenu > li').hide(); // Admin Menu (sidebar) Links
    $('#normal-sortables').children().hide(); // First set of sortables
    $('#dashboard_primary').hide(); // Second set of sortables
    $('#postbox-container-3').hide();
    $('#postbox-container-4').hide();
    $('#show-settings-link').hide();

    /**
     * EXPLICIT SHOWS
     */
    // ADMIN BAR LINKS
    $('#wp-admin-bar-wp-logo').show();
    $('#wp-admin-bar-site-name').show();

    // SIDEBAR LINKS
    $('#menu-dashboard').show();
    $('#menu-pages').show();
    $('#menu-users').show();

    // DASHBOARD ITEMS
    $('#dashboard_right_now').show();
    $('#dashboard_activity').show();

    // META BOXES
    $('#peer_reviewer_info_metabox').show();

});
