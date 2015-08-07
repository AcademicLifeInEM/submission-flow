jQuery(document).ready(function($) {

    var firstName1 = $('#PR_first_name_1');
    var lastName1 = $('#PR_last_name_1');
    var emailAddress1 = $('#PR_email_1');
    var backgroundInfo1 = $( "textarea[name|='PR_background_info_1']" );

    var firstName2 = $('#PR_first_name_2');
    var lastName2 = $('#PR_last_name_2');
    var emailAddress2 = $('#PR_email_2');
    var backgroundInfo2 = $( "textarea[name|='PR_background_info_2']" );

    var coauthorBg1 = $('textarea[name|="coauthor_1_background"]');
    var coauthorBg2 = $('textarea[name|="coauthor_2_background"]');
    var coauthorBg3 = $('textarea[name|="coauthor_3_background"]');
    var coauthorBg4 = $('textarea[name|="coauthor_4_background"]');

    requiredInputStatus();
    submitButtonGateway();

    $.each([firstName1, lastName1, emailAddress1, backgroundInfo1], function( index, item ){
        item.change(function(){
            requiredInputStatus();
            submitButtonGateway();
            emailCheck();
        });
    });
    $.each([firstName2, lastName2, emailAddress2, backgroundInfo2], function( index, item ){
        item.change(function(){
            optionalInputStatus();
            submitButtonGateway();
            emailCheck();
        });
    });

    function requiredInputStatus() {
        $.each([firstName1, lastName1, emailAddress1, backgroundInfo1], function(index, item){
            if (item.val() === '') {
                item.addClass('form-invalid');
            } else {
                item.removeClass('form-invalid');
            }
        });
    }

    function optionalInputStatus() {
        $.each([firstName2, lastName2, emailAddress2, backgroundInfo2], function(index, item){
            if (item.val() === '') {
                item.addClass('form-invalid');
            } else {
                item.removeClass('form-invalid');
            }
        });
        if ( allEmpty($('#peer_reviewer_2') ) ) {
            $('#peer_reviewer_2').children('input, textarea').removeClass('form-invalid');
        }
    }

    function submitButtonGateway() {

        // If none are empty in 1 and all are empty in 2
        if ( !anyEmpty( $('#peer_reviewer_1') ) && allEmpty( $('#peer_reviewer_2') ) ) {
            $('#publish').prop('disabled', false);
        }
        // If none are empty in 1 or 2
        else if ( !anyEmpty( $('#peer_reviewer_1') ) && !anyEmpty( $('#peer_reviewer_2') ) ) {
            $('#publish').prop('disabled', false);
        } else {
            $('#publish').prop('disabled', true);
        }

    }

    function emailCheck() {

        if ( emailAddress1.val() !== '' ) {
            if ( !IsEmail(emailAddress1.val()) ) {
                if ( !$('#invalid-email1').length ) {
                    $('#email_label1').prepend(' <p id="invalid-email1" style="color: red; text-align: center; font-weight: 600; padding:0px; margin: 0px;">**Email Address Invalid**</p>');
                }
                emailAddress1.addClass('form-invalid');
                $('#publish').prop('disabled', true);
            } else {
                $('#invalid-email1').remove();
                emailAddress1.removeClass('form-invalid');
                submitButtonGateway();
            }
        }

        if ( emailAddress2.val() !== '' ) {
            if ( !IsEmail(emailAddress2.val()) ) {
                if ( !$('#invalid-email2').length ) {
                    $('#email_label2').prepend(' <p id="invalid-email2" style="color: red; text-align: center; font-weight: 600; padding:0px; margin: 0px;">**Email Address Invalid**</p>');
                }
                emailAddress2.addClass('form-invalid');
                $('#publish').prop('disabled', true);
            } else {
                $('#invalid-email2').remove();
                emailAddress2.removeClass('form-invalid');
                submitButtonGateway();
            }
        }

    }

    // HELPER FUNCTIONS

    function allEmpty( parent ) {
        var obj = '';
        parent.children('input, textarea').each(function() {
            obj += $(this).val();
        });
        return(obj === '');
    }

    function anyEmpty( parent ) {
        var obj = false;
        parent.children('input, textarea').each(function() {
            if ( $(this).val() === '' ) {
                obj = true;
            }
        });
        return obj;
    }

    function IsEmail(email) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
    }



    /**
     * FUNCTIONS TO HANDLE COAUTHOR META BOX FIELDS
     */

     $.each([coauthor_2_first_name, coauthor_2_first_name, coauthor_2_first_name], function() {
         if ( $(this).val() !== '' ) {
             $(this).parent().removeClass('js-hide');
         }
     });


     var clickIterator = 2;

     $('.inside').on('click', '#add_coauthor', function(){

         if (clickIterator < 4) {
             $('#coauthor_' + clickIterator + '_div').removeClass('js-hide');
             clickIterator++;
         } else if (clickIterator == 4) {
             $('#coauthor_' + clickIterator + '_div').removeClass('js-hide');
             $('#add_coauthor').prop('disabled', true);
         }

     });

     // Toggle button handler
     if ( $('#PR_first_name_2').val() !== '' ) {
         $('#peer_reviewer_2').removeClass('js-hide');
         $("button[name|='toggle_second_reviewer']").addClass('js-hide');
     }


     $("button[name|='toggle_second_reviewer']").click(function(event) {

        $(this).hide();
        $('#peer_reviewer_2').removeClass('js-hide');

     });

});
