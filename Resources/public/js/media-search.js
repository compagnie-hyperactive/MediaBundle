/**
 * Created by nicolas on 21/04/17.
 */
$(function() {

    // Search
    $(document).on('click', "button", function(e) {

        if($(this).attr('id') != "search-button") {
            return;
        }

        e.preventDefault();

        var $form = $(this).closest('form')[0];

        var data = {}
        var searchParams = {};

        // Loop on length-1 elements to avoid storing submit button which is form element
        for(var i=0;i<($form.elements.length)-1;i++) {
            searchParams[$form.elements[i].name] = $form.elements[i].value;
            // formData.append($form.elements[i].name, $form.elements[i].value);
        }
        data.search = searchParams;

        //Add list parameters
        if($form.attributes.getNamedItem('data-type')) {
            data.type = $form.attributes.getNamedItem('data-type').value;
        } else {
            data.type = 'all';
        }

        data.libraryMode = true;

        var $mediaList = $(this).closest('.library-parent').find('.isotope');
        var $modal = $(this).closest('.modal');

        // Reload list
        jQuery.ajax({
            url: Routing.generate('lch_media_search'),
            data: data,
            type: 'POST',
            success: function (html) {
                // Isotope presentation
                $mediaList.isotope();

                $mediaList.isotope( 'remove', $(".media-list").find(".media") );
                $mediaList.isotope('insert', $(html).find(".media"));

                attachMediaItemHandlers($modal, $mediaList, extractRandId($modal.attr('id')));
            }
        });

        return false;
    });
})