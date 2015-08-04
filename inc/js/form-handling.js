jQuery(document).ready(function($) {

    var firstName = $('#PR_first_name');
    var lastName = $('#PR_last_name');
    var emailAddress = $('#PR_email');
    var backgroundInfo = $( "textarea[name|='PR_background_info']" );

    checkInputStatus();

    firstName.change(function(){
        checkInputStatus();
    });
    lastName.change(function(){
        checkInputStatus();
    });
    emailAddress.change(function(){
        checkInputStatus();
    });
    backgroundInfo.change(function(){
        checkInputStatus();
    });

    function checkInputStatus() {
        if (firstName.val() !== '' && lastName.val() !== '' && emailAddress.val() !== '' && backgroundInfo.val() !== '') {
            $('#publish').prop('disabled', false);
        } else {
            $('#publish').prop('disabled', true);
        }
        $.each([firstName, lastName, emailAddress, backgroundInfo], function(index, item){
            if (item.val() === '') {
                item.addClass('form-invalid');
            } else {
                item.removeClass('form-invalid');
            }
        });
    }



});
