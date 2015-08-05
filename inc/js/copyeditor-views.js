jQuery(document).ready(function($) {

    // HIDE EMPTY COPYEDITOR FIELDS
    $.each([$('#coauthor_2_first_name'), $('#coauthor_3_first_name'), $('#coauthor_4_first_name')], function(index, value){
        if (value.val() === '') {
            value.parent().hide();
        }
    });

    // HIDE ADD COAUTHOR BUTTON
    $('#add_coauthor').hide();

    // ADJUST TEXTAREA WIDTH
    $('#coauthor_container textarea').prop('cols', '44');

    // PREVENT EDITS
    $('#coauthor_container').children().children().prop('disabled', 'true');
    $('#peer_reviewer_meta_box > .inside').children().prop('disabled', 'true');


});
