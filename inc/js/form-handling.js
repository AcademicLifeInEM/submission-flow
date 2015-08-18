jQuery(document).ready(function($) {

    /**
     * FIXME:
     * KNOWN ISSUES:
     * - Leak somewhere that allows addition of another coauthor if email is
     * 	 changed from a working email to a bad email.
     */


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


    $.each([firstName1, lastName1, emailAddress1, credentials1], function( index, item ){
        item.change(function(){
            requiredInputStatus();
            emailCheck();
            submitButtonGateway();
            buttonGateway();
        });
    });
    $.each([firstName2, lastName2, emailAddress2, credentials2, twitterHandle2], function( index, item ){
        item.change(function(){
            optionalInputStatus();
            emailCheck();
            submitButtonGateway();
        });
    });

    $('.photo_holder').bind('DOMSubtreeModified', function() {
        coauthorRequiredStatus();
        addCoauthorHandler();
        emailCheck();
        buttonGateway();
        submitButtonGateway();
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

        var reviewerRequires = $('#peer_reviewer_meta_box').find('.form-invalid');
        var reviewerVisibleRows = $('#peer_reviewer_meta_box>.inside').children('div').not('.js-hide');
        var reviewerPhotoFields = reviewerVisibleRows.find('.inserted_headshot');
        var reviewerFilledRows = reviewerPhotoChecker();

        var coauthorRequires = $('#coauthor_details').find('.form-invalid');
        var coauthorVisibleRows = $('#coauthor_details').children('div').not('.js-hide');
        var coauthorPhotoFields = coauthorVisibleRows.find('.inserted_headshot');
        var coauthorFilledRows = coauthorRequiredStatus();

        if ( coauthorRequires.length > 0 || reviewerRequires.length > 0 || coauthorPhotoFields.length !== coauthorFilledRows || reviewerPhotoFields.length !== reviewerFilledRows ) {
            $('#publish').prop('disabled', true);
        } else {
            $('#publish').prop('disabled', false);
        }

    }

    function buttonGateway() {

        $('#SF_photo_1, #SF_photo_2, #SF_photo_3, #SF_photo_4, #SF_photo_5, #SF_photo_6, #SF_photo_7').each(function(){
            var theParent = $(this).parent().parent();
            var hasInvalid = theParent.find('.form-invalid').length;

            var allFilled = true;
            theParent.find('.js-required').each(function(){

                if ( $(this).val() === '' ) {
                    allFilled = false;
                    return false;
                }

            });

            if ( hasInvalid > 0 && $(this).hasClass('inserted_headshot') === false ) {
                $(this).addClass('button-required');
            } else if ( allFilled && $(this).hasClass('inserted_headshot') === false ) {
                $(this).addClass('button-required');
            } else {
                $(this).removeClass('button-required');
            }

        });

    }

    function emailCheck() {

        var emailFields = $('#peer_reviewer_meta_box, #coauthor_details').find('[id*="email"]');

        var badEmailExists = false;

        emailFields.each(function(){
            if ( $(this).val() !== '' ) {
                if ( !isEmail( $(this).val() ) ) {
                    $(this).addClass('form-invalid');
                    badEmailExists = true;
                } else {
                    $(this).removeClass('form-invalid');
                }
            }

            if ( badEmailExists ) {
                if ( $('#invalid-email-alert').hasClass('js-hide') ) {
                    $('#invalid-email-alert').removeClass('js-hide');
                }
            } else {
                if ( !$('#invalid-email-alert').hasClass('js-hide') ) {
                    $('#invalid-email-alert').addClass('js-hide');
                }
            }
        });
        return badEmailExists;
    }


    function reviewerPhotoChecker() {

        var filledCounter = 0;

        $('#peer_reviewer_meta_box>.inside').children('div').not('.js-hide').each(function(){

            var allFieldsEmpty = true;
            $(this).find('.js-required').each(function(){
                if ( $(this).val() !== '' ) {
                    allFieldsEmpty = false;
                    filledCounter++;
                    return false;
                }
            });
        });

        return filledCounter;

    }



    // HELPER FUNCTIONS

    function allEmpty( parent ) {
        var obj = '';
        parent.find('.js-required').each(function() {
            obj += $(this).val();
        });
        return(obj === '');
    }

    function anyEmpty( parent ) {
        var obj = false;
        parent.find('.js-required').each(function() {
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
     *  ADD SECOND REVIEWER BUTTON
     */

    // Toggle button handler
    if ( $('#PR_first_name_2').val() !== '' ) {
        $('#peer_reviewer_2').removeClass('js-hide');
        $("button[name|='toggle_second_reviewer']").addClass('js-hide');
    }


    $("button[name|='toggle_second_reviewer']").click(function(event) {

       $(this).hide();
       $('#peer_reviewer_2').removeClass('js-hide');

    });


    /**
     * FUNCTIONS TO HANDLE COAUTHOR META BOX FIELDS
     */

    var clickIterator = 2;

     // SHOW COAUTHOR SECTIONS IF THEY ARE FILLED OUT
    $.each([$('#coauthor_2_div'), $('#coauthor_3_div'), $('#coauthor_4_div')], function() {
        if ( $(this).find('[id$="_first_name"]').val() !== '' ) {
            $(this).removeClass('js-hide');
            clickIterator++;
        }
    });

    // AFTER ALL COAUTHORS SHOWN, DISABLE ADD BUTTON
    $('.inside').on('click', '#add_coauthor', function(){
        if (clickIterator < 5) {
            $('#coauthor_' + clickIterator + '_div').removeClass('js-hide');
            $('#add_coauthor').prop('disabled', true);
            clickIterator++;
        }
    });


    //////////////////////////////////////////////
    // ----------COAUTHOR FIELD LOGIC---------- //
    //////////////////////////////////////////////


    var allFields = $('#coauthor_details').find('input, textarea');

    allFields.each(function(){
        $(this).change(function() {
            coauthorRequiredStatus();
            addCoauthorHandler();
            emailCheck();
            buttonGateway();
            submitButtonGateway();
        });
    });

    $('.photo_container').bind('DOMSubtreeModified', function() {
        coauthorRequiredStatus();
        addCoauthorHandler();
        emailCheck();
        buttonGateway();
        submitButtonGateway();
    });


    function coauthorRequiredStatus() {

        var filledCounter = 0;

        $('#coauthor_details').children().not('.js-hide').each(function(){

            var allFieldsEmpty = true;
            $(this).find('.js-required').each(function(){
                if ( $(this).val() !== '' ) {
                    allFieldsEmpty = false;
                    filledCounter++;
                    return false;
                }
            });

            if ( !allFieldsEmpty ) {
                $(this).find('.js-required').each(function(){
                    if ( $(this).val() === '' ) {
                        $(this).addClass('form-invalid');
                    } else if ( $(this).is('[name$="_email"]') && !isEmail($(this).val()) ) {
                        $(this).addClass('form-invalid');
                    }
                    else {
                        $(this).removeClass('form-invalid');
                    }
                });
            } else {
                $(this).find('.js-required').removeClass('form-invalid');
            }

        });

        return filledCounter;
    }


    // HANDLER FOR ADD COAUTHOR BUTTON
    function addCoauthorHandler(){

        var disableButton = false;
        var visibleRows = $('#coauthor_details').children('div').not('.js-hide');
        var photoFields = visibleRows.find('.inserted_headshot');
        var badEmail = emailCheck();

        $('#coauthor_details').children().not('.js-hide').find('.js-required').each(function(){
            if ( $(this).val() === '' ) {
                disableButton = true;
                return false;
            }
        });

        $('#coauthor_details').children().not('.js-hide').find('[id$="_url"]').each(function(){
            if ( $(this).val() === '' || $(this).siblings('[id^="SF_photo_"]').is('button') ) {
                disableButton = true;
                return false;
            }
        });

        if ( disableButton || photoFields.length < visibleRows.length || clickIterator > 4 || badEmail ) {
            $('#add_coauthor').prop('disabled', true);
        } else {
            $('#add_coauthor').prop('disabled', false);
        }

    }

    requiredInputStatus();
    submitButtonGateway();
    addCoauthorHandler();
    coauthorRequiredStatus();
    buttonGateway();

});
