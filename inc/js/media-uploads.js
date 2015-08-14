jQuery(document).ready(function($) {

    // var SF_photo_1, SF_photo_1_url, SF_photo_1_holder

    // Media frame placeholder variables
    var SF_photo_1_frame, SF_photo_2_frame,
        SF_photo_3_frame, SF_photo_4_frame, SF_photo_5_frame, SF_photo_6_frame;

    var holderArray = [
        SF_photo_1_frame, SF_photo_2_frame,
        SF_photo_3_frame, SF_photo_4_frame, SF_photo_5_frame, SF_photo_6_frame
    ];

    var hiddenUrlArray = [
        '#SF_photo_1_url', '#SF_photo_2_url',
        '#SF_photo_3_url', '#SF_photo_4_url', '#SF_photo_5_url', '#SF_photo_6_url'
    ];

    $.each(['#SF_photo_1', '#SF_photo_2',
            '#SF_photo_3', '#SF_photo_4', '#SF_photo_5', '#SF_photo_6', ], function(index, item){
            $(item).live('click', function(e){

                e.preventDefault();
                var thisIdName = $(this).attr('ID');

                // If the frame already exists, re-open it.
                if ( holderArray[index] ) {
                    holderArray[index].open();
                    return;
                }

                holderArray[index] = wp.media.frames.SF_photo = wp.media({
                    title: meta_image.title,
                    button: { text:  meta_image.button },
                    library: { type: 'image' }
                });

                holderArray[index].on('select', function(){
                    var media_attachment = holderArray[index].state().get('selection').first().toJSON();
                    $(hiddenUrlArray[index]).val(media_attachment.url);
                    $(item).replaceWith(
                        '<div id="SF_photo_' + (index + 1) + '" class="inserted_headshot has_hover" style="background-image: url(\'' + media_attachment.url + '\');"><div></div></div>'
                    );
                });

                // Opens the media library frame.
                holderArray[index].open();
            });
        });


});
