(function ($) {
    var file_frame;
    $('#nimble_portfolio_media_lib').live('click', function (event) {
        var imgfield = $(this).attr('rel');
        event.preventDefault();

        if (file_frame) {
            file_frame.open();
            return;
        }

        var _states = [new wp.media.controller.Library({
                filterable: 'uploaded',
                title: 'Select an Image',
                multiple: false,
                priority: 20
            })];

        file_frame = wp.media.frames.file_frame = wp.media({
            states: _states,
            button: {
                text: 'Insert URL'
            }
        });

        file_frame.on('select', function () {
            var attachment = file_frame.state().get('selection').first().toJSON();
            $('#' + imgfield).val(attachment.url);
        });

        file_frame.open();
    });

    // Quick edit box of Filter
    $("#the-list").on('click', 'a.editinline', function () {
        var tag_id = $(this).parents('tr').attr('id');
        var sort_order = $('.sort-order', '#' + tag_id).text();
        $(':input[name="sort-order"]', '.inline-edit-row').val(sort_order);
    });

    // color box control for admin
    $("input.color-rgb").spectrum({
        chooseText: "Select Color",
        preferredFormat: "hex",
        showInput: true
    });

    // color box control (with transparency) for admin
    $("input.color-rgba").spectrum({
        chooseText: "Select Color",
        preferredFormat: "hex",
        showInput: true,
        showAlpha: true
    });
        
    // Nimble Options Panel     
    if($('body').is('[class *= "nimble_portfolio_configuration"],[class *= "generate_shortcode_page"]')){
       
		$('.nimble-icons-list.genericons').select2({
			formatResult: function(result){
				if (result.id === undefined)	return result.text;
				
				return '<i class="genericon genericon-'+ result.text +'"></i> '+ result.text;
			},
			formatSelection: function add_icon(result){

				if (result.id === undefined)	return result.text;
						
				return '<i class="genericon genericon-'+ result.text +'"></i> '+ result.text;
			},
		});
		
		$(document).on('change','#post_type-select',function(){
			var post_type = $(this).val();
            var _this = $(this);            
            $.get(ajaxurl,
                    {
                        'action': 'nimble_portfolio_shortcode_post_type_change',
                        'post_type': post_type
                    },
            function (response) {
				$('#taxonomies-select').find('option').remove().end();
				var options = $(response).find('select').prevObject[2].options;
				
				$(options).each(function(){
					var option = $(this);				
					$('#taxonomies-select').append('<option value="' + option.context.value +'">'+ option.context.innerHTML +'</option>');
				});
				$('#taxonomies-select').trigger('change');
            });
		});
		
		$(document).on('change','#taxonomies-select',function () {
            var taxonomy = $(this).val();            
            var _this = $(this);
            $.get(ajaxurl,
                    {
                        'action': 'nimble_portfolio_taxonomy_change',
                        'taxonomy': taxonomy,
                    },
            function (response) {
                $('#filter-select').html(response).select2("val", "");
                if($('#default_filter_isotope-select').length !== 0){
					$('#default_filter_isotope-select').html(response).select2("val", "");
				}
                jQuery.redux.initFields();
            })
            
        });
		
		$(document).on('click change','.gen_shortcode_class',function(event){
			
				event.preventDefault();
			
				var post_type = jQuery('#post_type-select').val();
				var taxonomy = jQuery('#taxonomies-select').val();
				var skin = jQuery('#skin-select').val();
				var lightbox = jQuery('#lightbox-select').val();
				var default_skin_filters = $('[id *= "_filter-select"]').select2("val");
				var default_skin_column_type = $('#default_skin_column-select').val();
				var default_skin_style = $('#default_skin_style-select').val();
				var items_per_page = $('#items_per_page').val();
				var hide_filters = jQuery('#hide_filters').val();
				var orderby = jQuery('#order_by-select').val();
				var order = jQuery('#order-select').val();
				var params = event.result ? event.result : '';
				
				//isotope fields
				var isotope_default_filter = $('#default_filter_isotope-select').val();
				var multi_filter_selection = $('#multi_filter_selection').val() ? $('#multi_filter_selection').val() : 0;
				var use_AND_logic_multi_filter = $('#use_AND_logic_multi_filter').val() ? $('#use_AND_logic_multi_filter').val() : 0;
				var ajax_filtering = $('#ajax_filtering').val() ? $('#ajax_filtering').val() : 0;
				var ajax_pagination = $('#ajax_pagination').val() ? $('#ajax_pagination').val() : 0;
							
				var params = '';

				if (post_type && post_type !== 'portfolio') {	params += 'post_type="' + post_type + '" ';	}

				if (taxonomy && taxonomy !== 'nimble-portfolio-type') {	params += 'taxonomy="' + taxonomy + '" '; }
				
				if (lightbox && lightbox !== 'prettyphoto') {	params += 'lightbox="' + lightbox + '" ';	}
				
				if (skin && skin !== 'default') {	params += 'skin="' + skin + '" ';	}
				
				if (default_skin_filters.length !== 0) {	params += ' filters="' + default_skin_filters + '" '; }
				
				if(skin == 'default' || skin == ''){
					if (default_skin_column_type && default_skin_column_type !== '-columns3') {	params += ' skin_columns="' + default_skin_column_type + '" '; }
					
					if (default_skin_style && default_skin_style !== '-normal') {	params += ' skin_style="' + default_skin_style + '" '; }
				}
				
				if($('#multi_filter_selection').length !== undefined){
					if (isotope_default_filter) {	params += 'default_filter="' + isotope_default_filter + '" ';	}
					if (multi_filter_selection) {	params += 'multi_filters="' + multi_filter_selection + '" ';	}
					if (use_AND_logic_multi_filter) {	params += 'multi_filters_and="' + use_AND_logic_multi_filter + '" ';	}
					if (ajax_filtering) {	params += 'ajax_filter="' + ajax_filtering + '" ';	}
					if (ajax_pagination) {	params += 'ajax_paginate="' + ajax_pagination + '" ';	}
				}

				
				if (items_per_page) {	params += ' items_per_page="' + items_per_page + '" '; }

				if (hide_filters == '1') {	params += 'hide_filters="' + hide_filters + '" '; }

				if (orderby && orderby !== 'menu_order') {	params += 'orderby="' + orderby + '" ';	}

				if (order && order !== 'ASC') {	params += 'order="' + order + '" ';	}

				jQuery(document).on("nimble_portfolio_shortcode_param", function (event, obj) {	params += event.result ? event.result : '';	});

				jQuery(document).trigger("nimble_portfolio_shortcode_param", {});

				var output = '[nimble-portfolio ' + params + ']';
				
				$('#shortcode_area-textarea').val(output);
			
		});
		
		$(document).ready(function(){
			var skin = 'default';
			
			$('#divide-skin_start_divider + .form-table tbody > tr').each(function(){
				if($(this).hasClass(skin))	{ $(this).removeClass('field_hidden'); }
				else if(!$(this).hasClass('not-hidden')) { $(this).addClass('field_hidden'); }
			});
		});
		
		$(document).on('change','#skin-select',function(){
			var skin = $('#skin-select').val() !== '' ? $('#skin-select').val() : 'default' ;
			
			$('#divide-skin_start_divider + .form-table tbody > tr').each(function(){
				if($(this).hasClass(skin))	{ $(this).removeClass('field_hidden'); }
				else if(!$(this).hasClass('not-hidden')) { $(this).addClass('field_hidden'); }
			});
			jQuery.redux.initFields();
		});
		
	}
		
})(jQuery);
