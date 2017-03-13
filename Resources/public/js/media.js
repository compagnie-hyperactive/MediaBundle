// TODO make all this available as jquery plugin
// (function ( $ ) {
//     var LchMedia = {
//         changeRandomId: function () {
//             alert('change');
//         }
//     };
// }( $ ));


$(document).ready(function(){
    // TODO find better way to check already handled
    var idModal = 'image-modal__';
    var idModalSave = 'image-modal-save__';
    var inputName = 'image__';
    var thumbName = 'imageThumb__';

    $('body').on('show.bs.modal', 'div[id^='+idModal+']', function (e) {
        var id = $(this).attr('id');
        var randId = extractRandId(id);
        var addRoute = $(this).attr('data-route-add');
        var mediaType = $(this).attr('data-media-type');
        loadAddMediaForm(randId, addRoute, mediaType);
    });

    $('body').on('click', 'button[id^='+idModalSave+']', function(e){
        var id = $(this).attr('id');
        var randId = extractRandId(id);
        save();
    });

    $('body').on('shown.bs.tab', 'div[id^='+idModal+'] a[data-toggle="tab"]', function (e) {
        var $parentModal = $(this).parents('div[id^='+idModal+']');
        var id = $parentModal.attr('id');
        var randId = extractRandId(id);
        var listRoute = $parentModal.attr('data-route-list');
        var mediaType = $parentModal.attr('data-media-type');

        if(e.target.hash == "#list-media__" + randId) {
            loadListMediaForm(randId, listRoute, mediaType);
        }
    })


    /**
     * Extract the random id generated
     *
     * @param string
     * @returns {*}
     */
    function extractRandId(string)
    {
        var numberPattern = /\d+/g;
        return string.match( numberPattern )[0];
    }

    function resetFormElement(e) {
        e.wrap('<form>').closest('form').get(0).reset();
        e.unwrap();
    }

    /**
     * Load the add Media Form in the modal
     *
     * @param randId
     * @param addRoute
     * @param mediaType
     */
    function loadAddMediaForm(randId, addRoute, mediaType)
    {
        var $modal = $('#'+idModal+randId);
        var fileValue;
        var data = {'id' : $('div[id="'+inputName+randId+'"] input[type=hidden]').val() };

        // Load addition route by default
        jQuery.ajax({
            url : Routing.generate(addRoute, {'type': mediaType}),
            type: 'POST',
            data : data,
            success: function(html) {
                var formName = $(html).attr('name');

                $modal.find('div.modal-body #add-media__' + randId).empty();
                $modal.find('div.modal-body #add-media__' + randId).append(
                    html
                );

                // Set file helper
                $('div#'+idModal+randId+' p.fileHelper').html($('#'+inputName+randId).find('input[type=hidden]').attr('data-helper'));

                // TODO review extension check
                // var $fileError = $('#'+idModal+randId + ' #fileError');
                $('div#'+idModal+randId+' #lch_media_bundle_image_file').on('change', function() {
                //
                //     fileValue = $(this).val();
                //
                //     // Remove Path form the filename
                //     var fileValueTpm = fileValue.split('\\');
                //     fileValue = fileValueTpm[fileValueTpm.length -1];
                //
                //     // Remove file extension form the filename
                //     fileValueTpm = fileValue.split('.');
                //
                //     // store file extension
                //     var fileExtension = fileValueTpm.pop();
                //
                //     fileValue = fileValueTpm.join('.');
                //
                //     var $hiddenInput = $('#image-'+randId).find('input[type=hidden]');
                //
                //     // Clean file Errors
                //     $fileError.empty();
                //
                //     // Control file extension
                //     var attrDataFormat = $hiddenInput.attr('data-format');
                //     if (typeof attrDataFormat !== typeof undefined && attrDataFormat !== false) {
                //         var allowedFormat = attrDataFormat.split(',');
                //         if (allowedFormat.indexOf(fileExtension) == -1) {
                //             $fileError.append("<p>Le fichier doit être au format <strong>"+$hiddenInput.attr('data-format')+'</strong></p>');
                //             $('div#'+idModal+randId+' #lch_media_bundle_image_file').val('');
                //         }
                //     }
                // TODO review limit checks
                //     var file = this.files[0];
                //     if( file ) {
                //         var img = new Media();
                //
                //         img.src = window.URL.createObjectURL( file );
                //
                //         img.onload = function() {
                //             var width = img.naturalWidth,
                //                 height = img.naturalHeight;
                //             window.URL.revokeObjectURL( img.src );
                //
                //
                //             // Control minimum width
                //             var minWidth = $hiddenInput.attr('data-min_width');
                //             if (typeof minWidth !== typeof undefined && minWidth !== false) {
                //                 if (minWidth > width) {
                //                     $fileError.append("<p>Le fichier doit avoir une largeur supérieur à <strong>" + minWidth + ' px</strong></p>');
                //                 }
                //             }
                //             // Control maximum width
                //             var maxWidth = $hiddenInput.attr('data-max_width');
                //             if (typeof maxWidth !== typeof undefined && maxWidth !== false) {
                //                 if (maxWidth < width) {
                //                     $fileError.append("<p>Le fichier doit avoir une largeur inférieur à <strong>" + maxWidth + ' px</strong></p>');
                //                 }
                //             }
                //
                //             // Control minimum height
                //             var minHeight = $hiddenInput.attr('data-min_height');
                //             if (typeof minHeight !== typeof undefined && minHeight !== false) {
                //                 if (minHeight > height) {
                //                     $fileError.append("<p>Le fichier doit avoir une hauteur supérieur à <strong>" + minHeight + ' px</strong></p>');
                //                 }
                //             }
                //
                //             // Control maximum height
                //             var maxHeight = $hiddenInput.attr('data-max_height');
                //             if (typeof maxHeight !== typeof undefined && maxHeight !== false) {
                //                 if (maxHeight < height) {
                //                     $fileError.append("<p>Le fichier doit avoir une hauteur inférieur à <strong>" + maxHeight + ' px</strong></p>');
                //                 }
                //             }
                //
                //             if ($.trim($fileError.html())=='') {
                //                 var $fileAlt = $('div#'+idModal+randId+' #lch_media_bundle_image_alt');
                //                 if ($fileAlt.val() == '') {
                //                     $fileAlt.attr('value',fileValue);
                //                 }
                //
                //                 var $fileName = $('div#'+idModal+randId+' #lch_media_bundle_image_name');
                //                 if ($fileName.val() == '') {
                //                     $fileName.attr('value',fileValue);
                //                 }
                //             } else {
                //                 $('div#'+idModal+randId+' #lch_media_bundle_image_file').val('');
                //             }
                //         };
                //     }
                });

                $('form[name='+formName+']').on('submit', function(e) {
                    e.preventDefault();

                    // TODO handle alt and name server side
                    // var $fileAlt = $('div#'+idModal+randId+' #lch_media_bundle_image_alt');
                    // if ($fileAlt.val() == '') {
                    //     $fileAlt.val(fileValue);
                    // }
                    //
                    // var $fileName = $('div#'+idModal+randId+' #lch_media_bundle_image_name');
                    // if ($fileName.val() == '') {
                    //     $fileName.val(fileValue);
                    // }

                    // var formdata = (window.FormData) ? new FormData($(this)) : null;
                    // var data = (formdata !== null) ? formdata : $(this).serialize();

                    var formData = new FormData($(this)[0]);
                    // var formData = $(this).serialize();

                    jQuery.ajax({
                        url : $(html).attr('action'),
                        type: 'POST',
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function(result) {
                            if("success" in result) {
                                setChosenMedia(result, randId);
                                $modal.modal('toggle');
                            } else {
                                $modal.find('div.modal-body #add-media__' + randId)
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
                })
            }
        });
    }


    /**
     * Load the add Media Form in the modal
     *
     * @param randId
     * @param listRoute
     * @param mediaType
     */
    function loadListMediaForm(randId, listRoute, mediaType)
    {
        var $modal = $('#'+idModal+randId);
        var $listTab = $modal.find('div.modal-body #list-media__' + randId);
        // var fileValue;

        // Load list route
        jQuery.ajax({
            url : Routing.generate(listRoute, { type: mediaType }),
            type: 'GET',
            success: function(html) {
                // var formName = $(html).attr('name');

                $listTab.empty();
                $listTab.append(
                    html
                );

                // // Add "choose" button
                $listTab.append(
                    "<button type='submit' class='btn btn-primary select'>Choose</button>"
                );
                // Handle media selection in list
                $listTab.find("div.media").on('click', function(e) {
                    $listTab.find("div.media").removeClass('chosen');
                    $(this).addClass('chosen');
                });


                // Handle media final choosing
                $listTab.find("button[type='submit']").on('click', function(e) {
                    e.preventDefault();
                    // TODO handle multiple
                    var $chosen = $listTab.find("div.media.chosen");
                    var entity = {
                        id: $chosen.attr('data-id'),
                        url: $chosen.attr('data-url'),
                        name: $chosen.attr('data-name'),
                    }
                    setChosenMedia(entity, randId);
                    $modal.modal('toggle');
                });
            }
        });
    }

    function setChosenMedia(entity, randId) {
        var $mediaControl = $('div[id="'+inputName+randId+'"]');
        $mediaControl.find('input[type=hidden]').val(entity.id);
        var thumbId = thumbName + randId;
        var thumbSelector = "div#" + thumbId;
        if ($mediaControl.find(thumbSelector  + ' img').length) {
            $mediaControl.find(thumbSelector + ' img').attr('src', entity.url);
        } else {
            $mediaControl.find(thumbSelector).html('<img src="'+entity.url+'" width="150"/>');
        }

        $mediaControl.find('p[id^=display]').text(entity.name);
    }

});