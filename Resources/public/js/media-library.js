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

    if($('#popin-mode').length > 0 && $('#popin-mode').val() == 1) {
        var popinMode = true;

        // Remove unwanted elements in popin mode
        $('div#main-menu').remove();
        $('header#horizontal-nav').remove();

    } else {
        var popinMode = false;
    }

    if(!popinMode) {
        /**
         * Handle type change
         */
        $('#media-type-selector').find('select').change(function() {
            window.location.href = Routing.generate('lch_admin_media_library', {type: $(this).val()});
        });


        /**
         * Handle modal interaction (show, hide, delete)
         */
        var $modal = $("#details-modal");

        // Show
        $('.media-list').on('click', '.media', function() {
            // Fill media data
            $modal.find('button[type="submit"]')
                .attr('data-id', $(this).attr('data-id'))
                .attr('data-type', $(this).attr('data-type'))
            ;
            $modal.find('p.alert-danger')
                .text("")
                .addClass('hidden')
            ;

            $modal.find('ul#media-details').html("");
            $modal.find('ul#media-details').append('<li>URL du média : <strong>' + $(this).attr('data-url') + '</strong></li>');
            if($(this).data('preview')) {
                $modal.find('.modal-body').append('<div class="text-center" style="border: 1px solid #ddd"><img src="' + $(this).data('preview') + '" /></div>');
            }

            $("#details-modal").modal('show');
        });

        // Delete
        $modal.find('button[type="submit"]').click(function(e) {
            if($(this).attr('data-id') && $(this).attr('data-type')) {

                var $mediaItem = $(".media-list .media[data-id='" + $(this).attr('data-id') + "']");
                // Delete
                jQuery.ajax({
                    url: Routing.generate('lch_media_delete', {id: $(this).attr('data-id'), type: $("#media-type-selector select").val() }),
                    type: 'DELETE',
                    success: function (data) {
                        if(data instanceof Object && "success" in data) {
                            // Close modal
                            $modal.modal('hide');

                            // Remove item from list
                            $mediaList
                                .isotope( 'remove', $mediaItem )
                                .isotope('layout')
                            ;
                        }
                        else {
                            $modal.find('p.alert-danger')
                                .text(data.error + " - " + data.message)
                                .removeClass('hidden')
                            ;
                        }
                    }
                });
            }
        });



        /**
         * Handle form submission and media creation
         */
        $('#media-form-container').find('form').submit(function() {
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
                            url: Routing.generate('lch_media_list', {type: result.type, libraryMode: true}),
                            type: 'GET',
                            success: function (html) {
                                // Remove empty list text if any
                                $mediaList.find('.text-center.alert.alert-warning').remove();
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
    }
});