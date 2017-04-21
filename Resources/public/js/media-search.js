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
        if($("#media-type-selector").find("select").length > 0) {
            data.type = $("#media-type-selector").find("select").val();
        } else {
            data.type = 'all';
        }

        data.libraryMode = true;

        var $mediaList = $(this).closest('.library-parent').find('.isotope');

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
            }
        });

        return false;
    });
})