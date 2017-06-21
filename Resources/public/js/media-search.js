/**
 * Created by nicolas on 21/04/17.
 */
$(function() {

    // Search
    $(document).on('click', "button", function(e) {

        if($(this).attr('id') != "search-button" && $(this).attr('id') != "more") {
            return;
        }
        $(this).find('.loader').toggleClass('hidden');

        e.preventDefault();

        var library = false;
        var $modal = $(this).closest('.modal');
        var $form = $modal.find('form.search');
        if($form.length === 0) {
            library = true;
            $form = $('form.search');
        }
        var $mediaList = $(this).closest('.library-parent').find('.isotope');

        var data = {}
        var searchParams = {};

        var page = $form.find('input[name="page"]').val();
        var more = false;

        if($(this).attr('id') == "more") {
            $form.find('input[name="page"]').val(parseInt($form.find('input[name="page"]').val()) + 1);
            more = true;
        }

        // Loop on length-1 elements to avoid storing submit button which is form element
        // for(var i=0;i<($form.elements.length)-1;i++) {
        //     searchParams[$form.elements[i].name] = $form.elements[i].value;
        //     // formData.append($form.elements[i].name, $form.elements[i].value);
        // }

        data.search = $form.serializeArray();

        // Add list parameters
        if($form.data('type')) {
            data.type = $form.data('type');
        } else {
            data.type = 'all';
        }

        data.libraryMode = true;

        var $button = $(this);
        // Reload list
        jQuery.ajax({
            url: Routing.generate('lch_media_search'),
            data: data,
            type: 'POST',
            success: function (html) {
                // Isotope presentation
                $mediaList.isotope();

                if(!more) {
                    $mediaList.isotope( 'remove', $(".media-list").find(".media") );
                }
                $mediaList.isotope('insert', $(html).find(".media"));
                if(!library) {
                    attachMediaItemHandlers($modal, $mediaList, extractRandId($modal.attr('id')));
                }
                $button.find('.loader').toggleClass('hidden');
            }
        });

        return false;
    });
})