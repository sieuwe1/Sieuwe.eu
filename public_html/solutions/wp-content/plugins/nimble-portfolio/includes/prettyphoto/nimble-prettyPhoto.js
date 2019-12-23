(function($) {

    if (typeof NimblePrettyPhoto == 'undefined')
        NimblePrettyPhoto = '{}';
    if (typeof NimblePrettyPhoto == "string")
        eval("NimblePrettyPhoto=" + NimblePrettyPhoto);

		$('body').append('<div id="pp_download_html"><a href="'+ NimblePrettyPhoto.wp_url +'/download-image.php" id="pp_download_image" alt="Download"><img src="'+ NimblePrettyPhoto.wp_url +'/img/pp_download.png" /></a></div>');


    NimblePrettyPhoto.social_tools = false;
    
    NimblePrettyPhoto.hook = 'data-rel';

    NimblePrettyPhoto.changepicturecallback = function() {
		
		 var _img = $pp_pic_holder.find("#pp_full_res img").attr("src");
            if(NimblePrettyPhoto.download_icon && _img) {
                var _stage = $pp_pic_holder.find(".pp_fade");
                _stage.find("#pp_download_image").remove();
                _stage.append($("#pp_download_html").html());
                _link = _stage.find("#pp_download_image");
                _link.attr('href', _link.attr('href') + '?img=' +_img);
            }

        if (typeof NimblePrettyPhoto.AddThis !== 'undefined' && typeof addthis !== 'undefined' ) {

            var _size = 'default';
            if (typeof NimblePrettyPhoto.AddThis.size !== 'undefined' && NimblePrettyPhoto.AddThis.size) {
                _size = NimblePrettyPhoto.AddThis.size + "x" + NimblePrettyPhoto.AddThis.size;
            }

            $pp_pic_holder.find("#nimble_portfolio_social").remove();
            $pp_pic_holder.find('.pp_fade').append('<div id="nimble_portfolio_social" class="addthis_toolbox addthis_' + _size + '_style"></div>');

            for (var i in NimblePrettyPhoto.AddThis.services) {
                $('#nimble_portfolio_social').append('<a class="addthis_button_' + NimblePrettyPhoto.AddThis.services[i] + '"></a>')
            }

            addthis.toolbox("#nimble_portfolio_social", {}, {url: pp_images[set_position], title: pp_titles[set_position] + " — " + pp_descriptions[set_position]});
        }
    };

    $(document).on('nimble_portfolio_lightbox', function(event, obj) {
        obj.items.prettyPhoto(NimblePrettyPhoto);
    });

})(jQuery);
