<?php

    /**
     * For full documentation, please visit: http://docs.reduxframework.com/
     * For a more extensive sample-config file, you may look at:
     * https://github.com/reduxframework/redux-framework/blob/master/sample/sample-config.php
     */

    if ( ! class_exists( 'Redux' ) ) {
        return;
    }

	/** Generate Shortcode **/
	$gen_shortcode = 'generate_shortcode_page';

    $args = array(
        'opt_name' => 'generate_shortcode_page',
        'display_name' => 'Nimble Portfolio &#8212; Generate Shortcode',
        'display_version' => '',
        'page_title' => 'Nimble Portfolio &#8212; Generate Shortcode',
        'menu_type' => 'submenu',
        'dev_mode' => false,
        'menu_title' => 'Generate Shortcode',
        'allow_sub_menu' => true,
        'page_parent' => 'edit.php?post_type=portfolio',
        'default_mark' => '',
        'show_import_export' => false,
        'open_expanded' => true,
        'hide_expand' => true,
        'admin_bar' => false,
        'disable_save_warn' => true,
        'hide_reset' => true,
        'hide_save' => true,
        'hints' => array(
            'icon_position' => 'right',
            'icon_color' => 'lightgray',
            'icon_size' => 'normal',
            'tip_style' => array(
                'color' => 'light',
            ),
            'tip_position' => array(
                'my' => 'top left',
                'at' => 'bottom right',
            ),
            'tip_effect' => array(
                'show' => array(
                    'duration' => '500',
                    'event' => 'mouseover',
                ),
                'hide' => array(
                    'duration' => '500',
                    'event' => 'mouseleave unfocus',
                ),
            ),
        ),
        'output_tag' => TRUE,
        'cdn_check_time' => '1440',
        'page_permissions' => 'manage_options',
        'database' => 'options',
        'transient_time' => '3600',
        'network_sites' => TRUE,
    );

   

    Redux::setArgs( $gen_shortcode, $args );

	Redux::setSection( $gen_shortcode, array(
        'title'      => __( 'Generate Shortcode', 'nimble_portfolio' ),
        'desc'       => __( '', 'nimble_portfolio' ),
        'id'         => 'generate-shortcode',
        'fields'     => array(
			array(
				'id'       => 'post_type',
				'type'     => 'select_extended',
				'class'    => 'gen_shortcode_class',
				'title'    => __('Post Type: ', 'nimble_portfolio'),
				'data' => 'post_types_shortcode',
				'default'  => 'portfolio'
			),
			/*
			array(
				'id'       => 'lightbox',
				'type'     => 'select_extended',
				'class'    => 'gen_shortcode_class',
				'title'    => __('Lightbox: ', 'nimble_portfolio'),
				'data' => 'nimble_portfolio_lightbox',
			),
			*/ 
			array(
				'id'       => 'taxonomies',
				'type'     => 'select_extended',
				'class'    => 'gen_shortcode_class',
				'title'    => __('Filters Type (Taxonomy): ', 'nimble_portfolio'),
				'data'	   => 'nimble_portfolio_taxonomy',
				'default'  => 'nimble-portfolio-type'
			),
			array(
				'id'       => 'filter',
				'type'     => 'select_extended',
				'class'    => 'gen_shortcode_class',
				'title'    => __('Filters: (no selection means ALL): ', 'nimble_portfolio'),
				'multi'	   => true,
				'data'  => 'nimble_portfolio_taxonomy_terms',
			),
			array(
				'id'       => 'hide_filters',
				'type'     => 'switch',
				'class'    => 'gen_shortcode_class',
				'title'    => __('Hide Filters', 'nimble_portfolio'), 
				'default' => false,
			),
			array(
				'id'       => 'order_by',
				'type'     => 'select',
				'class'    => 'gen_shortcode_class',
				'title'    => __('Order By: ', 'nimble_portfolio'),
				'options' => array(
					'author' => __('Author', 'nimble_portfolio'),
					'date'  => __('Date', 'nimble_portfolio'),
					'modified' => __('Last Modified Date', 'nimble_portfolio'),
					'comment_count' => __('Number of Comments', 'nimble_portfolio'),
					'ID'  => __('Post ID', 'nimble_portfolio'),
					'menu_order' => __('Page Order', 'nimble_portfolio'),
					'parent' => __('Post Parent ID', 'nimble_portfolio'),
					'name'  => __('Post Slug', 'nimble_portfolio'),
					'title' => __('Post Title', 'nimble_portfolio'),
					'rand' => __('Random', 'nimble_portfolio'),
				), 
				'default'	=> 'menu_order'
			),
			array(
				'id'       => 'order',
				'type'     => 'select',
				'class'    => 'gen_shortcode_class',
				'title'    => __('Order: ', 'nimble_portfolio'),
				'options' => array(
					'ASC'	=> __('Ascending', 'nimble_portfolio'),
					'DESC'	=> __('Descending', 'nimble_portfolio'),
				),
				'default'  => 'ASC'
			),
			array(
				'id'       => 'skin',
				'type'     => 'select_extended',
				'class'    => 'gen_shortcode_class',
				'title'    => __('Skin: ', 'nimble_portfolio'),
				'data'	   => 'nimble_portfolio_skin',
				'default'  => 'default'
			),
			array(
				'id'   =>'skin_start_divider',
				'type' => 'divide',
			),
			array(
				'id'		=> 'items_per_page',
				'type'		=> 'text',
				'class'    => 'gen_shortcode_class',
				'class'		=> 'no-border-bottom not-hidden gen_shortcode_class',
				'title'     => __('Items per page (enables pagination): ', 'nimble_portfolio'),
			),
			array(
				'id'   =>'skin_end_divider',
				'type' => 'divide',
				'title'=> __(' ', 'nimble_portfolio'),
			),
			array(
				'id'       => 'shortcode_area',
				'type'     => 'textarea',
				'title'    => __('Shortcode', 'nimble_portfolio'),
				'default'  => '[nimble-portfolio]'
			),
			array( 
				'id'       => 'raw-html',
				'type'     => 'raw',
				'title'    => __('<label class="ui-button ui-widget ui-state-default ui-button-text-only ui-corner-left ui-corner-right gen_shortcode_class" role="button" id="gen_shortcode_button"><span class="ui-button-text">Generate Shortcode</span></label>', 'nimble_portfolio'),
				'subtitle'    => __('&nbsp;', 'nimble_portfolio'),
				'desc'    => __('&nbsp;', 'nimble_portfolio'),
			),
        )
    ) );

    /** END Generate Shortcode **/


    // This is your option name where all the Redux data is stored.
    $opt_name = "nimble_portfolio_configuration";

    /**
     * ---> SET ARGUMENTS
     * All the possible arguments for Redux.
     * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
     * */

    $theme = wp_get_theme(); // For use with some settings. Not necessary.

    $args = array(
        'opt_name' => 'nimble_portfolio_configuration',
        'display_name' => 'Nimble Portfolio Configuration',
        'display_version' => '',
        'page_title' => 'Nimble Portfolio Configuration',
        'update_notice' => TRUE,
        'menu_type' => 'submenu',
        'dev_mode' => false,
        'admin_bar' => false,
        'menu_title' => 'Configuration',
        'page_parent' => 'edit.php?post_type=portfolio',
        'default_mark' => '',
        'hints' => array(
            'icon_position' => 'right',
            'icon_color' => 'lightgray',
            'icon_size' => 'normal',
            'tip_style' => array(
                'color' => 'light',
            ),
            'tip_position' => array(
                'my' => 'top left',
                'at' => 'bottom right',
            ),
            'tip_effect' => array(
                'show' => array(
                    'duration' => '500',
                    'event' => 'mouseover',
                ),
                'hide' => array(
                    'duration' => '500',
                    'event' => 'mouseleave unfocus',
                ),
            ),
        ),
        'output_tag' => TRUE,
        'cdn_check_time' => '1440',
        'page_permissions' => 'manage_options',
        'database' => 'options',
        'transient_time' => '3600',
        'network_sites' => TRUE,
    );

   

    Redux::setArgs( $opt_name, $args );

    /*** START GLOBAL SETTING SECTION ***/
    
    Redux::setSection( $opt_name, array(
        'title'      => __( 'Global Settings', 'nimble_portfolio' ),
        'desc'       => __( '', 'nimble_portfolio' ),
        'id'         => 'global-settings',
        'fields'     => array(
			array(
				'id'       => 'global_settings_loader_flag',
				'type'     => 'switch',
				'title'    => __('Enable Loader', 'nimble_portfolio'), 
				'validate_callback' => 'NimblePortfolioPlugin::validate_loader_settings_after',
				'default' => "0",
			),
			array(
				'id'       => 'global_settings_loader_color',
				'type'     => 'color',
				'title'    => __('Loader Color', 'nimble_portfolio'), 
				'default'  => '#000',
				'transparent' => false,
				'required' => array('global_settings_loader_flag','equals','1' ), 
			),
			array(
				'id'       => 'global_settings_loader_size',
				'type'     => 'select',
				'title'    => __('Loader Size: ', 'nimble_portfolio'), 
				'required' => array('global_settings_loader_flag','equals','1' ),
				'options'  => array(
					'10px' => 'Small',
					'20px' => 'Normal',
					'30px' => 'Large',
					'40px' => 'Extra Large',
				),
				'default'  => '10px',
			),
			array(
				'id'       => 'global_settings_thumb_nocache',
				'type'     => 'switch',
				'title'    => __('Force No Cache for Thumbnails: ', 'nimble_portfolio'), 
				'default' => false,
			),
			array(
				'id'       => 'global_settings_thumb_exact_size',
				'type'     => 'switch',
				'title'    => __('Force Exact Thumbnail Size Generation: ', 'nimble_portfolio'), 
				'default' => false,
			),
        )
    ) );
    
    /*** START PRETTYPHOTO SECTION ***/   
    
    Redux::setSection( $opt_name, array(
        'title'      => __( 'Prettyphoto Settings', 'nimble_portfolio' ),
        'desc'       => __( '', 'nimble_portfolio' ),
        'id'         => 'prettyphoto-settings',
        'fields'     => array(
			array(
				'id'       => 'prettyphoto_animation_speed',
				'type'     => 'select',
				'title'    => __('Animation Speed: ', 'nimble_portfolio'), 
				'options'  => array(
					'fast' => 'Fast',
					'slow' => 'Slow',
					'normal' => 'Normal',
				),
				'default'  => 'fast',
			),
			array(
				'id'       => 'prettyphoto_download_icon',
				'type'     => 'switch',
				'title'    => __('Download icon', 'nimble_portfolio'), 
				'default' =>  false,
			),
			array(
				'id'	=> 'prettyphoto_slideshow',
				'type'	=> 'text',
				'title' => __('Slideshow: ', 'nimble_portfolio'),
				'desc'	=> __('empty OR interval time in ms ', 'nimble_portfolio'),
				'default' =>  '5000',
				'validate' => 'numeric',
			),
			array(
				'id'       => 'prettyphoto_autoplay_slideshow',
				'type'     => 'switch',
				'title'    => __('Autoplay Slideshow', 'nimble_portfolio'), 
				'default' =>  false,
			),
			array(
				'id'       => 'prettyphoto_opacity',
				'type'     => 'select',
				'title'    => __('Opacity: ', 'nimble_portfolio'), 
				'options'  => array(
					'0.1' => '10%',
					'0.2' => '20%',
					'0.3' => '30%',
					'0.4' => '40%',
					'0.5' => '50%',
					'0.6' => '60%',
					'0.7' => '70%',
					'0.8' => '80%',
					'0.9' => '90%',
					'1.0' => '100%',										
				),
				'default'  => '0.8',
			),
			array(
				'id'       => 'prettyphoto_show_title',
				'type'     => 'switch',
				'title'    => __('Show title', 'nimble_portfolio'), 
				'default' =>  true,
			),
			array(
				'id'       => 'prettyphoto_allow_resize',
				'type'     => 'switch',
				'title'    => __('Allow Resize	', 'nimble_portfolio'),
				'desc'	   => __('Resize the photos bigger than viewport. true/false', 'nimble_portfolio'),
				'default' =>  true,
			),
			array(
				'id'	=> 'prettyphoto_default_width',
				'type'	=> 'text',
				'title' => __('Default width : ', 'nimble_portfolio'),
				'default'	=> '500',
				'validate' => 'numeric',
			),
			array(
				'id'	=> 'prettyphoto_default_height',
				'type'	=> 'text',
				'title' => __('Default height : ', 'nimble_portfolio'),
				'default'	=> '344',
				'validate' => 'not_empty',
			),
			array(
				'id'	=> 'prettyphoto_counter_separator_label',
				'type'	=> 'text',
				'title' => __('Counter Separator label : ', 'nimble_portfolio'),
				'default'	=> '/',
				'validate' => 'not_empty',
			),
			array(
				'id'       => 'prettyphoto_theme',
				'type'     => 'select',
				'title'    => __('Theme: ', 'nimble_portfolio'), 
				'options'  => array(
					'pp_default' => 'Default',
					'dark_rounded' => 'Dark Rounded',
					'dark_square' => 'Dark square',
					'facebook' => 'facebook',
					'light_rounded' => 'Light rounded',
					'light_square' => 'Light Square',									
				),
				'default'  => 'pp_default',
			),
			array(
				'id'	=> 'prettyphoto_horizontal_padding',
				'type'	=> 'text',
				'title' => __('Horizontal Padding : ', 'nimble_portfolio'),
				'desc' => __('The padding on each side of the picture', 'nimble_portfolio'),
				'default'	=> '20',
				'validate' => 'numeric',
			),
			array(
				'id'       => 'prettyphoto_hideflash',
				'type'     => 'switch',
				'title'    => __('Hide flash ', 'nimble_portfolio'),
				'desc'	   => __('Hides all the flash object on a page, set to TRUE if flash appears over prettyPhoto', 'nimble_portfolio'),
				'default' =>  false,
			),
			array(
				'id'       => 'prettyphoto_autoplay',
				'type'     => 'switch',
				'title'    => __('Autoplay Videos ', 'nimble_portfolio'),
				'desc'	   => __('Automatically start videos: True/False', 'nimble_portfolio'),
				'default' =>  true,
			),
			array(
				'id'       => 'prettyphoto_modal',
				'type'     => 'switch',
				'title'    => __('Modal ', 'nimble_portfolio'),
				'desc'	   => __('If set to true, only the close button will close the window', 'nimble_portfolio'),
				'default' =>  false,
			),
			array(
				'id'       => 'prettyphoto_deeplinking',
				'type'     => 'switch',
				'title'    => __('Deep linking ', 'nimble_portfolio'),
				'desc'	   => __('Allow prettyPhoto to update the url to enable deeplinking.', 'nimble_portfolio'),
				'default' =>  true,
			),
			array(
				'id'       => 'prettyphoto_overlay_gallery',
				'type'     => 'switch',
				'title'    => __('Overlay Gallery ', 'nimble_portfolio'),
				'desc'	   => __('If set to true, a gallery will overlay the fullscreen image on mouse over', 'nimble_portfolio'),
				'default' =>  true,
			),
			array(
				'id'       => 'prettyphoto_keyboard_shortcuts',
				'type'     => 'switch',
				'title'    => __('Keyboard Shortcuts ', 'nimble_portfolio'),
				'desc'	   => __('Set to false if you open forms inside prettyPhoto', 'nimble_portfolio'),
				'default' =>  true,
			),
			array(
				'id'       => 'prettyphoto_addthis_script',
				'type'     => 'textarea',
				'title'    => __('Addthis Script Tag ', 'nimble_portfolio'),
				'validate' => 'html',
			),
        )
    ) );

	/*** END PRETTYPHOTO SECTION ***/
