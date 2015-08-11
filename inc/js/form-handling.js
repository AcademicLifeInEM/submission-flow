jQuery(document).ready(function($) {

    var firstName1 = $('#PR_first_name_1');
    var lastName1 = $('#PR_last_name_1');
    var emailAddress1 = $('#PR_email_1');
    var credentials1 = $( "textarea[name|='PR_credentials_1']" );

    var firstName2 = $('#PR_first_name_2');
    var lastName2 = $('#PR_last_name_2');
    var emailAddress2 = $('#PR_email_2');
    var twitterHandle2 = $('#PR_twitter_handle_2');
    var credentials2 = $( "textarea[name|='PR_credentials_2']" );

    var coauthorBg1 = $('textarea[name|="coauthor_1_credentials"]');
    var coauthorBg2 = $('textarea[name|="coauthor_2_credentials"]');
    var coauthorBg3 = $('textarea[name|="coauthor_3_credentials"]');
    var coauthorBg4 = $('textarea[name|="coauthor_4_credentials"]');

    requiredInputStatus();
    submitButtonGateway();
    requireRow($('#coauthor_1_div'));

    $.each([firstName1, lastName1, emailAddress1, credentials1], function( index, item ){
        item.change(function(){
            requiredInputStatus();
            submitButtonGateway();
            emailCheck();
            requireRow( $('#coauthor_container div:not(.js-hide):last') );
        });
    });
    $.each([firstName2, lastName2, emailAddress2, credentials2, twitterHandle2], function( index, item ){
        item.change(function(){
            optionalInputStatus();
            submitButtonGateway();
            emailCheck();
            requireRow( $('#coauthor_container div:not(.js-hide):last') );
        });
    });

    function requiredInputStatus() {
        $.each([firstName1, lastName1, emailAddress1, credentials1], function(index, item){
            if (item.val() === '') {
                item.addClass('form-invalid');
            } else {
                item.removeClass('form-invalid');
            }
        });
    }

    function optionalInputStatus() {
        $.each([firstName2, lastName2, emailAddress2, credentials2], function(index, item){
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
        if ( !anyEmpty( $('#peer_reviewer_1') ) && allEmpty( $('#peer_reviewer_2') ) && $('#PR_twitter_handle_2').val() === '' ) {
            $('#publish').prop('disabled', false);
        }
        // If none are empty in 1 or 2
        else if ( !anyEmpty( $('#peer_reviewer_1') ) && !anyEmpty( $('#peer_reviewer_2') ) ) {
            $('#publish').prop('disabled', false);
        } else {
            $('#publish').prop('disabled', true);
        }

        if ( $('#coauthor_container>div').children().hasClass('form-invalid') || $('#peer_reviewer_1').children().hasClass('form-invalid') || $('#peer_reviewer_2').children().hasClass('form-invalid') ) {
            $('#publish').prop('disabled', true);
        }

    }

    function emailCheck() {

        if ( emailAddress1.val() !== '' ) {
            if ( !isEmail(emailAddress1.val()) ) {
                if ( $('#invalid-email-alert').hasClass('js-hide') ) {
                    $('#invalid-email-alert').removeClass('js-hide');
                }
                emailAddress1.addClass('form-invalid');
                $('#publish').prop('disabled', true);
            } else {
                if ( $('#coauthor_container div:not(.js-hide):last').children('input:nth-of-type(3)').val() === '' || isEmail( $('#coauthor_container div:not(.js-hide):last').children('input:nth-of-type(3)').val() ) ) {
                    $('#invalid-email-alert').addClass('js-hide');
                }
                emailAddress1.removeClass('form-invalid');
                submitButtonGateway();
            }
        }

        if ( emailAddress2.val() !== '' ) {
            if ( !isEmail(emailAddress2.val()) ) {
                if ( $('#invalid-email-alert').hasClass('js-hide') ) {
                    $('#invalid-email-alert').removeClass('js-hide');
                }
                emailAddress2.addClass('form-invalid');
                $('#publish').prop('disabled', true);
            } else {
                if ( $('#coauthor_container div:not(.js-hide):last').children('input:nth-of-type(3)').val() === '' || isEmail( $('#coauthor_container div:not(.js-hide):last').children('input:nth-of-type(3)').val() ) ) {
                    $('#invalid-email-alert').addClass('js-hide');
                }
                emailAddress2.removeClass('form-invalid');
                submitButtonGateway();
            }
        }

    }

    // HELPER FUNCTIONS

    function allEmpty( parent ) {
        var obj = '';
        parent.children('.js-required').each(function() {
            obj += $(this).val();
        });
        return(obj === '');
    }

    function anyEmpty( parent ) {
        var obj = false;
        parent.children('.js-required').each(function() {
            if ( $(this).val() === '' ) {
                obj = true;
            }
        });
        return obj;
    }

    function isEmail(email) {
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
             requireRow($('#coauthor_' + clickIterator + '_div'));
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

     //////////////////////////////////////////////
     // ----------COAUTHOR FIELD LOGIC---------- //
     //////////////////////////////////////////////

     $('#coauthor_container>div>.js-required').each(function(){
         $(this).change(function(){
             requireRow($(this).parent());
         });
     });

     function requireRow( parent ) {

         if ( !allEmpty( parent ) ) {
             parent.children('.js-required').each(function(){
                 if ( $(this).val() === '' ) {
                     $(this).addClass("form-invalid");
                     $('#publish').prop('disabled', true);
                     $('#add_coauthor').prop('disabled', true);
                 } else {
                     $(this).removeClass('form-invalid');
                     submitButtonGateway();
                 }
             });
             if ( parent.children('input:nth-of-type(3)') !== ''  ) {
                  $('#add_coauthor').prop('disabled', true);
             }
         } else {
             parent.children('.js-required').removeClass('form-invalid');
             submitButtonGateway();
             $('#add_coauthor').prop('disabled', true);
         }

         if ( anyEmpty( parent ) === false & isEmail( parent.children('input:nth-of-type(3)').val() ) ) {
             $('#add_coauthor').prop('disabled', false);
         }

         if ( $('#coauthor_container').children('.js-hide').length === 0 ) {
             $('#add_coauthor').prop('disabled', true);
         }

         if ( $('#coauthor_container div:not(.js-hide):last').children('input:nth-of-type(3)').val() !== '' & !isEmail( $('#coauthor_container div:not(.js-hide):last').children('input:nth-of-type(3)').val() ) ) {
             $('#invalid-email-alert').removeClass('js-hide');
             $('#coauthor_container div:not(.js-hide):last').children('input:nth-of-type(3)').addClass('form-invalid');
         } else {
             $('#invalid-email-alert').addClass('js-hide');
             emailCheck();
         }

         submitButtonGateway();

     }


});
