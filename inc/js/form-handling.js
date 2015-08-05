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


    /**
     * FUNCTIONS TO HANDLE COAUTHOR META BOX FIELDS
     */

     // If the 2nd through 4th fields are blank, hide them on load
     $.each([$('#coauthor_2_first_name'), $('#coauthor_3_first_name'), $('#coauthor_4_first_name')], function(index, value){
         if (value.val() === '') {
             value.parent().hide();
         }
     });

     var clickIterator = 2;

     $('.inside').on('click', '#add_coauthor', function(){

         if (clickIterator < 4) {
             $('#coauthor_' + clickIterator + '_div').show();
             clickIterator++;
         } else if (clickIterator == 4) {
             $('#coauthor_' + clickIterator + '_div').show();
             $('#add_coauthor').prop('disabled', true);
         }

     });

});
