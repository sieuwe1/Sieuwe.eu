(function($) {

    var gal_frame;

    $('#sep-gal-images').live('click', function(event) {
        if (confirm('Are you sure you want to remove all images from the gallery/album?')){
            $("#nimble_portfolio_gal_ids").val('');
            $(this).html('');
        }
    });


    $('#nimble_portfolio_open_gal').live('click', function(event) {
        var idsField = $($(this).attr('rel'));
        event.preventDefault();

        if (gal_frame) {
            gal_frame.open();
            return;
        }

        var _title, _state, _selection;
        if (idsField.val()) {
            _title = wp.media.view.l10n.editGalleryTitle;
            _state = 'gallery-edit'
            var shortcode = new wp.shortcode({
                tag: 'gallery',
                attrs: {ids: idsField.val()},
                type: 'single'
            });

            var attachments = wp.media.gallery.attachments(shortcode);

            var _selection = new wp.media.model.Selection(attachments.models, {
                props: attachments.props.toJSON(),
                multiple: true
            });

            _selection.gallery = attachments.gallery;

            _selection.more().done(function() {
                // Break ties with the query.
                _selection.props.set({query: false});
                _selection.unmirror();
                _selection.props.unset('orderby');
            });

        } else {
            _title = wp.media.view.l10n.addToGalleryTitle;
            _state = 'gallery-library';
            _selection = false;
        }

        gal_frame = wp.media({
            id: 'nimble-portfolio-sep-gal',
            frame: 'post',
            state: _state,
            title: _title,
            editing: true,
            multiple: true,
            selection: _selection
        });

        gal_frame.on('update', function() {
            var controller = gal_frame.states.get('gallery-edit');
            var library = controller.get('library');
            var ids = library.pluck('id');
            idsField.val(ids);
            $("#sep-gal-images").html($("#sep-gal-loader").html());
            $.get(ajaxurl,
                    {
                        'action': 'sep_gal_thumbs',
                        'sep-gal-ids': idsField.val()
                    },
            function(response) {
                $("#sep-gal-images").html(response);
            });

        });

        gal_frame.states.get('gallery-edit').gallerySettings = function() {
            return "";
        }

        gal_frame.open();
    });

})(jQuery);


jQuery(document).ready(function($) {

    $('.nimble-portfolio-default-config .color-picker').each(function() {
        var _input = $(this);
        var _picker = $(_input.attr('rel'));
        if (_picker.val().search("#") !== 0) {
            _picker.val("#" + _picker.val());
        }
        _input.hide();
        _input.farbtastic(_input.attr('rel'));
        _picker.click(function() {
            _input.slideToggle()
        });
    });

    $(".nimble-portfolio-default-config .tabs").tabs();

});
