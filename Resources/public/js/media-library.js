/**
 * Created by nicolas on 05/04/17.
 */
$(function(){
    var $mediaListContainer = $('#media-list-container');
    var $mediaFormContainer = $('#media-form-container');
    var $mediaList = $mediaListContainer.find('.media-list');
    // Isotope presentation
    $mediaList.isotope({
        itemSelector: '.media',
        percentPosition: true,
        masonry: {
            // use outer width of grid-sizer for columnWidth
            columnWidth: '.media'
        }
    });

    /**
     * Handle type change
     */
    $('#media-type-selector select').change(function() {
        window.location.href = Routing.generate('lch_admin_media_library', {type: $(this).val()});
    });

    /**
     * Handle form submission and media creation
     */
    $('form').submit(function() {
        var formData = new FormData($(this)[0]);

        jQuery.ajax({
            url : $(this).attr('action'),
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(result) {
                if(result instanceof Object && "success" in result) {
                    // Reload list
                    jQuery.ajax({
                        url: Routing.generate('lch_media_list', {type: result.type, choose: false}),
                        type: 'GET',
                        success: function (html) {
                            var $lastMediaInserted = $(html).find('.media').last();
                            $mediaList
                                .prepend($lastMediaInserted)
                                .isotope( 'prepended', $lastMediaInserted )
                            ;
                        }
                    });
                } else {
                    $mediaFormContainer
                        .empty()
                        .html(result)
                    ;
                }

            },
            error: function (xhr, status, error) {
                // $modal.find('div.modal-body').empty();
                // $modal.find('div.modal-body').html(
                //     xhr.responseText
                // );
            }
        });

        return false;
    });
});