jQuery(document).ready(function($) {

    // HIDE ADD REVIEWER & ADD COPYEDITOR BUTTONS
    $('button[name|="toggle_second_reviewer"]').addClass('js-hide');
    $('#add_coauthor').removeClass('button-secondary').addClass('js-hide');

    // REMOVE HOVER EFFECT ON IMAGES;
    $('#peer_reviewer_meta_box').find('.has_hover').removeClass('has_hover');
    $('#coauthor_details').find('.has_hover').removeClass('has_hover');

    // HIDE EMPTY COPYEDITOR / PEER REVIEWER FIELDS
    $.each([$('#coauthor_2_first_name'), $('#coauthor_3_first_name'), $('#coauthor_4_first_name')], function(){
        if ( $(this).val() !== '') {
            $(this).parent().removeClass('js-hide');
        }
    });
    if ( $('#coauthor_1_first_name').val() === '' ) {
        $('#coauthor_meta_box').hide();
    }

    // SHOW SECOND REVIEWER IF THERE IS ONE
    if ( $('#PR_first_name_2').val() !== '' ) {
        $('#peer_reviewer_2').removeClass('js-hide');
    }

    // ADJUST TEXTAREA WIDTH
    $('#coauthor_container textarea').prop('cols', '44');

    // PREVENT EDITS
    $('#coauthor_container').children().children().prop('disabled', 'true');
    $('#peer_reviewer_1').children().prop('disabled', 'true');
    $('#peer_reviewer_2').children().prop('disabled', 'true');


});
