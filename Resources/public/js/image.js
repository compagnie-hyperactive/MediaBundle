$(document).ready(function(){

    var idModal = 'image-modal-';
    var idModalSave = 'image-modal-save-';
    var inputName= 'image-';

    $('div[id^='+idModal+']').on('show.bs.modal', function (e) {
        var id = $(this).attr('id');
        var randId = extractRandId(id);
        var addRoute = $(this).attr('data-route-add');
        var listRoute = $(this).attr('data-route-list');
        loadMediaForm(randId, addRoute, listRoute);
    });

    $('button[id^='+idModalSave+']').on('click',function(e){
        var id = $(this).attr('id');
        var randId = extractRandId(id);
        save();
    });



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
     * @param listRoute
     */
    function loadMediaForm(randId, addRoute, listRoute)
    {
        var modal = $('#'+idModal+randId);
        var fileValue;
        var data = {'id' : $('div[id="'+inputName+randId+'"] input[type=hidden]').val() };

        // Load addition route by default
        jQuery.ajax({
            url : Routing.generate(addRoute),
            type: 'post',
            data : data,
            success: function(html) {
                var formName = $(html).attr('name');

                modal.find('div.modal-body #add-media').empty();
                modal.find('div.modal-body #add-media').append(
                    html
                );

                // Set file helper
                $('div#'+idModal+randId+' p.fileHelper').html($('#'+inputName+randId).find('input[type=hidden]').attr('data-helper'));

                var $fileError = $('#'+idModal+randId + ' #fileError');
                $('div#'+idModal+randId+' #lch_media_bundle_image_file').on('change', function() {

                    fileValue = $(this).val();

                    // Remove Path form the filename
                    var fileValueTpm = fileValue.split('\\');
                    fileValue = fileValueTpm[fileValueTpm.length -1];

                    // Remove file extension form the filename
                    fileValueTpm = fileValue.split('.');

                    // store file extension
                    var fileExtension = fileValueTpm.pop();

                    fileValue = fileValueTpm.join('.');

                    var $hiddenInput = $('#image-'+randId).find('input[type=hidden]');

                    // Clean file Errors
                    $fileError.empty();

                    // Control file extension
                    var attrDataFormat = $hiddenInput.attr('data-format');
                    if (typeof attrDataFormat !== typeof undefined && attrDataFormat !== false) {
                        var allowedFormat = attrDataFormat.split(',');
                        if (allowedFormat.indexOf(fileExtension) == -1) {
                            $fileError.append("<p>Le fichier doit être au format <strong>"+$hiddenInput.attr('data-format')+'</strong></p>');
                            $('div#'+idModal+randId+' #lch_media_bundle_image_file').val('');
                        }
                    }

                    var file = this.files[0];
                    if( file ) {
                        var img = new Media();

                        img.src = window.URL.createObjectURL( file );

                        img.onload = function() {
                            var width = img.naturalWidth,
                                height = img.naturalHeight;
                            window.URL.revokeObjectURL( img.src );


                            // Control minimum width
                            var minWidth = $hiddenInput.attr('data-min_width');
                            if (typeof minWidth !== typeof undefined && minWidth !== false) {
                                if (minWidth > width) {
                                    $fileError.append("<p>Le fichier doit avoir une largeur supérieur à <strong>" + minWidth + ' px</strong></p>');
                                }
                            }
                            // Control maximum width
                            var maxWidth = $hiddenInput.attr('data-max_width');
                            if (typeof maxWidth !== typeof undefined && maxWidth !== false) {
                                if (maxWidth < width) {
                                    $fileError.append("<p>Le fichier doit avoir une largeur inférieur à <strong>" + maxWidth + ' px</strong></p>');
                                }
                            }

                            // Control minimum height
                            var minHeight = $hiddenInput.attr('data-min_height');
                            if (typeof minHeight !== typeof undefined && minHeight !== false) {
                                if (minHeight > height) {
                                    $fileError.append("<p>Le fichier doit avoir une hauteur supérieur à <strong>" + minHeight + ' px</strong></p>');
                                }
                            }

                            // Control maximum height
                            var maxHeight = $hiddenInput.attr('data-max_height');
                            if (typeof maxHeight !== typeof undefined && maxHeight !== false) {
                                if (maxHeight < height) {
                                    $fileError.append("<p>Le fichier doit avoir une hauteur inférieur à <strong>" + maxHeight + ' px</strong></p>');
                                }
                            }

                            if ($.trim($fileError.html())=='') {
                                var $fileAlt = $('div#'+idModal+randId+' #lch_media_bundle_image_alt');
                                if ($fileAlt.val() == '') {
                                    $fileAlt.attr('value',fileValue);
                                }

                                var $fileName = $('div#'+idModal+randId+' #lch_media_bundle_image_name');
                                if ($fileName.val() == '') {
                                    $fileName.attr('value',fileValue);
                                }
                            } else {
                                $('div#'+idModal+randId+' #lch_media_bundle_image_file').val('');
                            }
                        };
                    }
                });

                $('form[name='+formName+']').on('submit', function(e) {
                    e.preventDefault();

                    var $fileAlt = $('div#'+idModal+randId+' #lch_media_bundle_image_alt');
                    if ($fileAlt.val() == '') {
                        $fileAlt.val(fileValue);
                    }

                    var $fileName = $('div#'+idModal+randId+' #lch_media_bundle_image_name');
                    if ($fileName.val() == '') {
                        $fileName.val(fileValue);
                    }

                    var formdata = (window.FormData) ? new FormData($(this)[0]) : null;
                    var data = (formdata !== null) ? formdata : $(this).serialize();

                    jQuery.ajax({
                        url : $(html).attr('action'),
                        type: 'post',
                        data: data,
                        contentType: false,
                        processData: false,
                        success: function(entity) {

                            $('div[id="'+inputName+randId+'"] input[type=hidden]').val(entity.id);
                            if ($('div[id="'+inputName+randId+'"] div#imageThumb img').length) {
                                $('div[id="'+inputName+randId+'"] div#imageThumb img').attr('src', entity.url);
                            } else {
                                $('div[id="'+inputName+randId+'"] div#imageThumb').html('<img src="'+entity.url+'" width="150"/>');
                            }

                            $('div[id="'+inputName+randId+'"] p#displayMediaName-'+randId).text(entity.name);

                            modal.modal('toggle');
                        },
                        error: function (xhr, status, error) {
                            modal.find('div.modal-body').empty();
                            modal.find('div.modal-body').html(
                                xhr.responseText
                            );
                        }
                    });
                    return false;
                })
            }
        });
    }
});