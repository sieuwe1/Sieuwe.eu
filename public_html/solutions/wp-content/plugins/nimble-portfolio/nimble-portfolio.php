<?php
/*
  Plugin Name: Nimble Portfolio
  Plugin URI: http://nimble3.com/demo/nimble-portfolio-free/
  Description: Using this free plugin you can transform your portfolio in to a cutting edge jQuery powered gallery that lets you feature and sort your work like a pro.
  Version: 3.0.1
  Author: Nimble3
  Author URI: http://www.nimble3.com/
  License: GPLv2 or later
 */

include("includes/class.NimblePortfolio.php");
include("includes/class.NimblePortfolioItem.php");
include("includes/class.NimblePortfolioSkin.php");
include("includes/class.NimblePortfolioShortcodeWidget.php");
include("includes/class.NimblePortfolioRecentItemsWidget.php");
include("includes/prettyphoto/nimble-prettyphoto.php");
include("admin/admin-init.php");

if (!class_exists('NimblePortfolioPlugin')) {

    class NimblePortfolioPlugin {
     
        static private $version;
        static private $postType;
        static private $postTypeSlug;
        static private $taxonomy;
        static private $taxonomySlug;
        static private $dirPath;
        static private $dirUrl;
        static private $options;

        static function init($params = array()) {
            self::$version = '3.0.1';
            self::$postType = 'portfolio';
            self::$postTypeSlug = apply_filters('nimble_portfolio_posttype_slug', 'portfolio');
            self::$taxonomy = 'nimble-portfolio-type';
            self::$taxonomySlug = apply_filters('nimble_portfolio_taxonomy_slug', 'portfolio-filter');
            self::$dirPath = dirname(__FILE__);
            self::$dirUrl = self::path2url(self::$dirPath);
            self::$options = null;

            add_theme_support('post-thumbnails', array(self::$postType));
            add_image_size('portfolio_col_thumb', 100, 100, true);

            add_filter('attribute_escape', array(__CLASS__, 'renameMenuTitle'), 10, 2);

            register_activation_hook(__FILE__, array(__CLASS__, 'onActivate'));
            register_deactivation_hook(__FILE__, array(__CLASS__, 'onDeactivate'));

            add_action('init', array(__CLASS__, 'registerPostType'));
            add_action('init', array(__CLASS__, 'tinymceShortcodeButton'));

            add_shortcode('nimble-portfolio', array(__CLASS__, 'getPortfolio'));

            add_action('wp_head', array(__CLASS__, 'enqueueStyle'),1);
            add_action('wp_head', array(__CLASS__, 'enqueueScript'));

            add_filter('manage_' . self::$postType . '_posts_columns', array(__CLASS__, 'adminPostsColumns'));
            add_action('manage_' . self::$postType . '_posts_custom_column', array(__CLASS__, 'adminPostsCustomColumn'));

            // Custom Fields for Taxonomy
            add_action(self::$taxonomy . '_edit_form_fields', array(__CLASS__, 'taxonomyEditFormField'));
            add_action(self::$taxonomy . '_add_form_fields', array(__CLASS__, 'taxonomyAddFormField'));
            add_action('edited_' . self::$taxonomy, array(__CLASS__, 'saveTaxonomyValue'));
            add_action('create_' . self::$taxonomy, array(__CLASS__, 'saveTaxonomyValue'));
            add_action('manage_edit-' . self::$taxonomy . '_columns', array(__CLASS__, 'taxonomyColumnHeader'));
            add_action('manage_' . self::$taxonomy . '_custom_column', array(__CLASS__, 'taxonomyCustomValue'), 10, 3);
            add_action('quick_edit_custom_box', array(__CLASS__, 'taxonomyQuickEditField'), 10, 3);

            // Admin Handlers
            add_action('admin_head', array(__CLASS__, 'adminHead'));
            add_action('admin_menu', array(__CLASS__, 'adminOptions'));
            add_action('current_screen', array(__CLASS__, 'adminScreen'));
            add_action('save_post', array(__CLASS__, 'updateData'), 1, 2);

            add_action('wp_ajax_nimble_portfolio_tinymce', array(__CLASS__, 'ajaxTinymceShortcodeParams'));
            add_action('wp_ajax_nimble_portfolio_tinymce_skin_change', array(__CLASS__, 'ajaxTinymceSkinChange'));
            add_action('wp_ajax_nimble_portfolio_tinymce_post_type_change', array(__CLASS__, 'ajaxTinymcePostTypeChange'));

            add_action('wp_ajax_nimble_portfolio_shortcode_skin_change', array(__CLASS__, 'ajaxShortcodeGenSkinChange'));
            add_action('wp_ajax_nimble_portfolio_shortcode_post_type_change', array(__CLASS__, 'ajaxShortcodeGenPostTypeChange'));
            
            add_action('wp_ajax_nimble_portfolio_taxonomy_change', array(__CLASS__, 'ajaxShortcodeTaxonomyChange'));
            add_filter('redux/options/generate_shortcode_page/section/generate-shortcode',array(__CLASS__,'DefaultSkinGenerateShortcodefields'),10,1);
            
            add_filter('redux/options/nimble_portfolio_configuration/sections',array(__CLASS__,'DefaultSkinOptionsPanel'),10,1);
            add_filter('redux/nimble_portfolio_configuration/aURL_filter',array(__CLASS__,'checkadsurl'),10,1);
            add_filter('redux/validate/nimble_portfolio_configuration/before_validation',array(__CLASS__,'validate_loader_settings_before'),10,2);
            
            
			add_action('plugins_loaded', array(__CLASS__, 'nimble_portfolio_textdomain'));
			
            do_action('nimble_portfolio_init');
        }
        
        static function checkadsurl($url){
			
			return '';
			
		}
              
        static function DefaultSkinOptionsPanel($sections){
			$skin_options = array(
					array(
						'title'      => __( 'Default Skin Settings', 'nimble_portfolio' ),
						'desc'       => __( '', 'nimble_portfolio' ),
						'id'         => 'default-skin-settings',
						'fields'     => array()
					),	
					array(
						'title'      => __( 'Title', 'nimble_portfolio' ),
						'id'         => 'default-skin-settings-title',
						'subsection' => true,
						'fields'     => array(
							array(
								'id'       => 'default_skin_title-position',
								'type'     => 'select',
								'title'    => __('Position: ', 'nimble_portfolio'), 
								'options'  => array(
									'before' => 'Before Thumbnail',
									'after' => 'After Thumbnail',
									'inside' => 'Inside Thumbnail',
								),
								'default'  => 'before',
							),
							array(
								'id'       => 'default_skin_title-color',
								'type'     => 'color',
								'title'    => __('Color: ', 'nimble_portfolio'),
								'validate' => 'color', 
								'transparent' => false,
							),
							array(
								'id'       => 'default_skin_title-bgcolor',
								'type'     => 'color',
								'title'    => __('Background Color: ', 'nimble_portfolio'), 
								'validate' => 'color', 
								'transparent' => false,
							),
						)
					),
					array(
						'title'      => __( 'Filter/Pagination Link', 'nimble_portfolio' ),
						'id'         => 'default-skin-settings-filter',
						'subsection' => true,
						'fields'     => array(	
							array(
								'id'       => 'default_skin_filter_link_color',
								'type'     => 'color',
								'title'    => __('Normal State - Color: ', 'nimble_portfolio'),
								'validate' => 'color',  
								'transparent' => false,
							),
							array(
								'id'       => 'default_skin_filter_bg_color',
								'type'     => 'color',
								'title'    => __('Normal State - Background: ', 'nimble_portfolio'), 
								'validate' => 'color', 
								'transparent' => false,
							),
							array(
								'id'       => 'default_skin_filter_border_color',
								'type'     => 'color',
								'title'    => __('Normal State - Border Color: ', 'nimble_portfolio'), 
								'transparent' => false,
							),	
							array(
								'id'   =>'divider_1',
								'type' => 'divide'
							),
							array(
								'id'       => 'default_skin_filter_link_color_hover',
								'type'     => 'color',
								'title'    => __('Selected / On Hover - Color: ', 'nimble_portfolio'),
								'validate' => 'color',  
								'transparent' => false,
							),
							array(
								'id'       => 'default_skin_filter_bg_color_hover',
								'type'     => 'color',
								'title'    => __('Selected / On Hover - Background: ', 'nimble_portfolio'), 
								'validate' => 'color', 
								'transparent' => false,
							),
							array(
								'id'       => 'default_skin_filter_border_color_hover',
								'type'     => 'color',
								'title'    => __('Selected / On Hover - Border Color: ', 'nimble_portfolio'), 
								'validate' => 'color', 
								'transparent' => false,
							),
						)
					),
					array(
						'title'      => __( 'Post Permalink', 'nimble_portfolio' ),
						'id'         => 'default-skin-settings-post-permalink',
						'subsection' => true,
						'fields'     => array(	
							array(
								'id'       => 'default_skin_readmore-flag',
								'type'     => 'switch',
								'title'    => __('Display? ', 'nimble_portfolio'),
								'default' =>  true,
							),
							array(
								'id'       => 'default_skin_readmore-text',
								'type'     => 'text',
								'title'    => __('Text ', 'nimble_portfolio'),
								'default'  => 'more →',
								'validate' =>  'not_empty',
							),
							array(
								'id'       => 'default_skin_readmore-color',
								'type'     => 'color',
								'title'    => __('Color ', 'nimble_portfolio'),
								'validate' => 'color',  
								'transparent' => false,
							),
						)
					),
					array(
						'title'      => __( 'Portfolio URL Link', 'nimble_portfolio' ),
						'id'         => 'default-skin-settings-portfolio-url',
						'subsection' => true,
						'fields'     => array(	
							array(
								'id'       => 'default_skin_viewproject-flag',
								'type'     => 'switch',
								'title'    => __('Display? ', 'nimble_portfolio'),
								'default' =>  true,
							),
							array(
								'id'       => 'default_skin_viewproject-text',
								'type'     => 'text',
								'title'    => __('Text ', 'nimble_portfolio'),
								'default'  => 'visit us →',
								'validate' =>  'not_empty',
							),
							array(
								'id'       => 'default_skin_viewproject-color',
								'type'     => 'color',
								'title'    => __('Color ', 'nimble_portfolio'), 
								'validate' => 'color', 
								'transparent' => false,
							),
						)
					),
					array(
						'title'      => __( 'Thumbnail', 'nimble_portfolio' ),
						'id'         => 'default-skin-settings-thumbnail',
						'subsection' => true,
						'fields'     => array(	
							array(
								'id'       => 'default_skin_thumb-size',
								'type'     => 'text',
								'title'    => __('Thumbnail Size ', 'nimble_portfolio'),
							),
							array(
								'id'       => 'default_skin_hover-icon',
								'type'     => 'select_extended',
								'title'    => __('Default Hover Icon ', 'nimble_portfolio'),
								'data'	   => 'genericons',
								'default'  => 'zoom',
								
							),
							array(
								'id'       => 'default_skin_hover-color',
								'type'     => 'color',
								'title'    => __('Hover Color ', 'nimble_portfolio'),
								'validate' => 'color',  
								'transparent' => false,
							),
							array(
								'id'       => 'default_skin_hover-bgcolor',
								'type'     => 'color',
								'title'    => __('Hover Background Color ', 'nimble_portfolio'), 
								'validate' => 'color', 
								'transparent' => false,
							),
						)
					),
			);
			return array_merge($sections,$skin_options);
		}
		
		static function getGlobalSettings() {			
			if (self::$options === null) {
                self::$options = self::getOptions();
            }
            
            return @self::$options['global-settings'];
        }


        static function path2url($path) {
            if (!defined('ABSPATH')) {
                return false;
            }
            return trim(site_url(), '/\\') . "/" . str_replace("\\", "/", trim(substr_replace($path, '', 0, strlen(ABSPATH)), '/'));
        }

        static function phpvar2htmlatt($atts) {
            $return = ' ';
            if (is_array($atts) && count($atts)) {
                foreach ($atts as $att => $val) {
                    $return .= $att . '="' . (is_array($val) ? implode(" ", $val) : $val) . '" ';
                }
            }
            return $return;
        }

        static function getVersion() {
            return self::$version;
        }

        static function getPostType() {
            return self::$postType;
        }

        static function getPostTypeSlug() {
            return self::$postTypeSlug;
        }

        static function getTaxonomy() {
            return self::$taxonomy;
        }

        static function getTaxonomySlug() {
            return self::$taxonomySlug;
        }

        static function getPath($tail) {
            $tail = trim($tail, '/');
            return self::$dirPath . ($tail ? "/$tail/" : "/");
        }

        static function getUrl($tail) {
            $tail = trim($tail, '/');
            return self::$dirUrl . ($tail ? "/$tail/" : "/");
        }

        static function updateData($post_id, $post) {


            if ($post->post_type == 'revision') {
                return;
            }

            if (!current_user_can('edit_post', $post->ID)) {
                return;
            }

            if (isset($_POST['sort-order']) && $post->menu_order != $_POST['sort-order']){
                $my_post = array(
                    'ID'           => $post->ID,
                    'menu_order' => $_POST['sort-order']
                );
                wp_update_post( $my_post );
            }

            if (!wp_verify_nonce(@$_POST['nimble_portfolio_noncename'], plugin_basename(__FILE__))) {
                return;
            }

            $mydata = array();
            $mydata['nimble-portfolio'] = $_POST['nimble_portfolio'];
            $mydata['nimble-portfolio-url'] = $_POST['nimble_portfolio_url'];

            $mydata = apply_filters('nimble_portfolio_update_data', $mydata);

            foreach ($mydata as $key => $value) { //Let's cycle through the $mydata array!
                update_post_meta($post->ID, $key, $value);
                if (!$value)
                    delete_post_meta($post->ID, $key); //delete if blank
            }
        }

        static function renameMenuTitle($safe_text, $text) {
            if (__('Portfolio Items', 'nimble_portfolio') !== $text) {
                return $safe_text;
            }

            remove_filter('attribute_escape', 'renameMenuTitle');

            return __('Nimble Portfolio', 'nimble_portfolio');
        }

        static function onActivate() {
            self::registerPostType();
            flush_rewrite_rules();
        }

        static function onDeactivate() {
            flush_rewrite_rules();
        }

		static function DefaultSkinGenerateShortcodefields($section){
			global $nimble_portfolio_configuration;	
			$class = 'default gen_shortcode_class';
			
			$fields = array(
			array( 
				'id'       => 'default_skin_heading',
				'type'     => 'raw',
				'class'	   => $class.' skin_head',
				'title'    => __('<h2 class="skin-head">Default Skin</h2>', 'nimble_portfolio'),
				'subtitle'    => __('<span style="display:none">&nbsp;</span>', 'nimble_portfolio'),
				'desc'    => __('<span style="display:none">&nbsp;</span>', 'nimble_portfolio'),
			),
			array(
				'id'       => 'default_skin_column',
				'type'     => 'select',
				'class'	   => $class,
				'title'    => __('Skin Columns: ', 'nimble_portfolio'),
				'options' => array(
					'-columns2' => __('2 Columns', 'nimble_portfolio'),
					'-columns3' => __('3 Columns', 'nimble_portfolio'),
					'-columns4' => __('4 Columns', 'nimble_portfolio'),
					'-columns5' => __('5 Columns', 'nimble_portfolio'),
				), 
				'default'	=> '-columns3'
			),
			array(
				'id'       => 'default_skin_style',
				'type'     => 'select',
				'class'	   => $class,
				'title'    => __('Skin Style: ', 'nimble_portfolio'),
				'options' => array(
					'-normal' => __('Normal', 'nimble_portfolio'),
					'-round'  => __('Round', 'nimble_portfolio'),
					'-square' => __('Square', 'nimble_portfolio'),
				), 
				'default'	=> '-normal'				
			));
			
			$index = 0;
			
			foreach($section['fields'] as $key => $field){
				if($field['id'] == 'skin_start_divider'){
					$index = array_search($key,array_keys($section['fields']));
				}
			}
			
			array_splice($section['fields'],++$index,0,$fields);
			
			return $section;
		}

        static function registerPostType() {

            $labels = array(
                'name' => __('Portfolio Items','nimble_portfolio'),
                'singular_name' => __('Portfolio Item','nimble_portfolio'),
                'add_new' => __('Add Portfolio Item','nimble_portfolio'),
                'add_new_item' => __('Add New Portfolio Item','nimble_portfolio'),
                'edit_item' => __('Edit Portfolio Item','nimble_portfolio'),
                'new_item' => __('New Portfolio Item','nimble_portfolio'),
                'view_item' => __('View Portfolio Item','nimble_portfolio'),
                'search_items' => __('Search Portfolio Items','nimble_portfolio'),
                'not_found' => __('No Portfolio Items found','nimble_portfolio'),
                'not_found_in_trash' => __('No Portfolio Items found in Trash','nimble_portfolio'),
                'parent_item_colon' => '',
                'menu_name' => __('Portfolio Items','nimble_portfolio')
            );

            $args = array(
                'labels' => $labels,
                'public' => true,
                'show_ui' => true,
                'capability_type' => 'post',
                'hierarchical' => true,
                'rewrite' => array('slug' => self::$postTypeSlug),
                'supports' => array(
                    'title',
                    'thumbnail',
                    'editor',
                    'excerpt',
                ),
                'menu_position' => 23,
                'menu_icon' => self::$dirUrl . '/includes/icon.png',
                'taxonomies' => array(self::$taxonomy)
            );

            $args = apply_filters('nimble_portfolio_post_type_args', $args);

            register_post_type(self::$postType, $args);

            self::registerTaxonomy(self::$postType);
        }

        static function registerTaxonomy($postType = null) {

            if ($postType === null) {
                return;
            }

            $labels = array(
                'name' => __('Filters','nimble_portfolio'),
                'singular_name' => __('Filter','nimble_portfolio'),
                'add_new' => __('Add Filter','nimble_portfolio'),
                'add_new_item' => __('Add New Filter','nimble_portfolio'),
                'edit_item' => __('Edit Filter','nimble_portfolio'),
                'new_item' => __('New Filter','nimble_portfolio'),
                'view_item' => __('View Filter','nimble_portfolio'),
                'search_items' => __('Search Filters','nimble_portfolio'),
                'not_found' => __('No Filters found','nimble_portfolio'),
                'not_found_in_trash' => __('No Filters found in Trash','nimble_portfolio'),
                'parent_item_colon' => '',
                'menu_name' => __('Filters','nimble_portfolio')
            );

            $args = array(
                'labels' => $labels,
                'hierarchical' => true,
                'query_var' => true,
                'rewrite' => array('slug' => self::$taxonomySlug)
            );

            $args = apply_filters('nimble_portfolio_taxonomy_args', $args);

            register_taxonomy(self::$taxonomy, $postType, $args);
        }

        static function tinymceShortcodeButton() {

            if (!current_user_can('edit_posts') && !current_user_can('edit_pages') && get_user_option('rich_editing') == 'true') {
                return;
            }

            add_filter("mce_external_plugins", array(__CLASS__, "registerTinymcePlugin"));
            add_filter('mce_buttons', array(__CLASS__, 'registerTinymceButton'));
        }

        static function registerTinymcePlugin($plugin_array) {
            $plugin_array['nimble_portfolio_button'] = self::$dirUrl . '/includes/tinymce-plugin.js';
            return $plugin_array;
        }

        static function registerTinymceButton($buttons) {
            $buttons[] = "nimble_portfolio_button";
            return $buttons;
        }

        static function ajaxTinymceShortcodeParams() {
            include "includes/tinymce-plugin.php";
            exit;
        }

        static function ajaxTinymceSkinChange() {
            do_action('nimble_portfolio_tinymce_skin_change', $_GET['skin']);
            exit;
        }

        static function ajaxTinymcePostTypeChange() {
            $post_type = $_GET['post_type'];
            $all_taxonomies = get_taxonomies(array('public' => true), 'names');
            $taxonomies = get_object_taxonomies($post_type, 'objects');
            if (count($taxonomies)) {
                ?>
                <label for="nimble_portfolio_tinymce_taxonomy"><?php _e("Filters Type (Taxonomy)", 'nimble_portfolio'); ?>:</label>
                <select id="nimble_portfolio_tinymce_taxonomy" name="nimble_portfolio_tinymce_taxonomy">
                    <?php
                    foreach ($taxonomies as $taxonomy => $taxonomy_obj) {
                        if (!in_array($taxonomy, $all_taxonomies)) {
                            continue;
                        }
                        ?>
                        <option value="<?php echo $taxonomy ?>"><?php echo "$taxonomy_obj->label ($taxonomy)"; ?></option>
                    <?php } ?>
                </select>
                <?php
            } else {
                _e("No taxonomy found under <strong>$post_type</strong> post type.", "nimble_portfolio");
            }
            exit;
        }

        static function ajaxShortcodeGenSkinChange() {
            do_action('nimble_portfolio_shortcode_skin_change', $_GET['skin']);
            exit;
        }
        
        static function ajaxShortcodeTaxonomyChange(){
			$tax_term = $_GET['taxonomy'];
			$categories = array();
			$categories_obj = get_categories(array('taxonomy' => $tax_term, 'hide_empty' => false ));
			foreach($categories_obj as $category){
				echo '<option value="'.$category->term_id.'">'. $category->name .'</option>' ;
			}
		}

        static function ajaxShortcodeGenPostTypeChange() {
            $post_type = $_GET['post_type'];
            $all_taxonomies = get_taxonomies(array('public' => true), 'names');
            $taxonomies = get_object_taxonomies($post_type, 'objects');
            $option_set = "";
            if (count($taxonomies)) {
                foreach ($taxonomies as $taxonomy => $taxonomy_obj) {
                    if (!in_array($taxonomy, $all_taxonomies)) {
                        continue;
                    }
                    $option_set .= "<option value='$taxonomy' " . selected($taxonomy, 'nimble-portfolio-type', false) . " >$taxonomy_obj->label ($taxonomy)</option>";
                }
            }
            ?>
            <label for="nimble_portfolio_shortcode_taxonomy"><?php _e("Filters Type (Taxonomy)", 'nimble_portfolio'); ?>:</label>
            <select id="nimble_portfolio_shortcode_taxonomy" name="nimble_portfolio_shortcode_taxonomy" <?php disabled($option_set, ""); ?>>
                <?php
                if ($option_set) {
                    echo $option_set;
                } else {
                    ?>
                    <option value=""><?php _e("No Taxnomy Found under the Post Type!", "nimble_portfolio"); ?></option>
                <?php } ?>
            </select>
            <?php
            exit;
        }

        static function getPortfolio($atts) {
            ob_start();
            self::showPortfolio($atts);
            $content = ob_get_clean();
            return $content;
        }

        static function showPortfolio($atts = array()) {

            $nimble_portfolio = new NimblePortfolio($atts);

            $nimble_portfolio->renderTemplate();
        }

        static function enqueueStyle() {
			
            $nimblesort = apply_filters('nimble_portfolio_sort_style', '');
            if ($nimblesort) {
                wp_enqueue_style('nimblesort-style', $nimblesort);
            }

            wp_enqueue_style('nimble-portfolio-style', file_exists(get_template_directory() . "/nimble-portfolio/nimble-portfolio.css") ? get_template_directory_uri() . "/nimble-portfolio/nimble-portfolio.css" : self::getUrl('includes') . "nimble-portfolio.css");

            do_action('nimble_portfolio_enqueue_style');
        }

        static function enqueueScript() {

            $nimblesort = apply_filters('nimble_portfolio_sort_script', self::getUrl('includes') . 'sort.js');
            if ($nimblesort) {
                wp_enqueue_script('nimblesort-script', $nimblesort, array('jquery'), self::$version);
            }

            $nimblesort = apply_filters('nimble_portfolio_sort_script', self::getUrl('includes') . 'nimble-portfolio.js');
            do_action('nimble_portfolio_enqueue_script');
        }

        static function adminHead() {
            wp_enqueue_style('nimble-portfolio-admin', self::getUrl('includes') . "admin.css", null, null, "all");
            wp_enqueue_style('nimble-portfolio-spectrum', self::getUrl('includes') . "spectrum/spectrum.css", null, null, "all");
            wp_register_script('nimble-portfolio-spectrum', self::getUrl('includes') . 'spectrum/spectrum.js', array('jquery'), '1.6.0');
            wp_register_script('nimble-portfolio-admin', self::getUrl('includes') . 'admin.js', array('jquery', 'nimble-portfolio-spectrum'), self::$version);
            wp_enqueue_script('nimble-portfolio-spectrum');
            wp_enqueue_script('nimble-portfolio-admin');
        }

        static function adminScreen() {

            $currentScreen = get_current_screen();

            if ($currentScreen->post_type === "portfolio" && $currentScreen->base === $currentScreen->id) {
                require_once("includes/class.NimblePortfolioLessC.php");
            }
        }

        static function adminOptions() {
			if (!version_compare(self::getVersion(), "3.0.0", 'ge') && !version_compare(self::getVersion(), "3.0.0-beta", 'eq')) {
				add_submenu_page('edit.php?post_type=' . self::$postType, __('Generate Shortcode','nimble_portfolio'), __('Generate Shortcode','nimble_portfolio'), 'manage_options', 'nimble-portfolio-gen-shortcode', array(__CLASS__, 'shortcodeGeneratorPage'));
			}
            do_action('nimble_portfolio_create_section_before', self::$postType);
            add_meta_box('nimble-portfolio-section-options', __('Options', 'nimble_portfolio'), array(__CLASS__, 'renderOptions'), self::$postType, 'normal', 'high');
            do_action('nimble_portfolio_create_section_after', self::$postType);
        }

        static function shortcodeGeneratorPage() {
            include("includes/shortcode-generator.php");
        }

        static function globalSettings() {
            include("includes/global-settings.php");
        }

        static function renderOptions($post) {
            $item = new NimblePortfolioItem($post->ID);
            ?>
            <div class="nimble-portfolio-meta-section">
                <div class="form-wrap">
                    <div class="form-field">
                        <label for="nimble_portfolio"><?php _e('Image/Video URL', 'nimble_portfolio') ?></label>
                        <input type="text" id="nimble_portfolio" name="nimble_portfolio" value="<?php echo esc_attr($item->getData('nimble-portfolio')); ?>" style="width:70%;" />
                        <a id="nimble_portfolio_media_lib" href="javascript:void(0);" class="button" rel="nimble_portfolio"><?php _e('URL from Media Library', 'nimble_portfolio') ?></a>
                        <p><?php _e('Enter URL for the full-size image or video (youtube, vimeo, swf, quicktime) you want to display in the lightbox gallery. You can also choose Image URL from your Media gallery <strong>(Please note: If this field is empty then Featured Image will be used in lightbox gallery)</strong>', 'nimble_portfolio') ?></p>
                    </div>            
                    <div class="form-field">
                        <label for="nimble_portfolio_url"><?php _e('Portfolio URL', 'nimble_portfolio') ?></label>
                        <input type="text" name="nimble_portfolio_url" value="<?php echo esc_attr($item->getData('nimble-portfolio-url')); ?>" />
                        <p><?php _e('Enter URL to the live version of the project.', 'nimble_portfolio') ?></p>
                    </div>            
                    <div class="form-field">
                        <label for="menu_order"><?php _e('Sort Order', 'nimble_portfolio') ?></label>
                        <input type="text" id="menu_order" name="menu_order" value="<?php echo esc_attr($item->menu_order); ?>" style="width: 100px;" />
                        <p><?php _e('Set the sort order for your item here with 0 being the first and so on.', 'nimble_portfolio') ?></p>
                    </div>
                    <?php do_action('nimble_portfolio_renderoptions_field', $item); ?>
                </div>
                <input type="hidden" name="nimble_portfolio_noncename" id="nimble_portfolio_noncename" value="<?php echo wp_create_nonce(plugin_basename(__FILE__)); ?>" />
            </div>
            <?php
        }

        static function adminPostsColumns($cols) {
            $cols['filters'] = __('Filters', 'nimble_portfolio');
            $cols['thumbnail'] = __('Thumbnail', 'nimble_portfolio');
            $cols['sort-order'] = __('Sort Order', 'nimble_portfolio');
            return $cols;
        }

        static function adminPostsCustomColumn($column_name) {
            $item = new NimblePortfolioItem(get_the_ID());
            if ($column_name == 'thumbnail') {
                echo '<img src="' . $item->getThumbnail('portfolio_col_thumb') . '" />';
            } elseif ($column_name == 'filters') {
                $_terms = $item->getFilters(self::$taxonomy, 'R');
                if (!empty($_terms) && !is_wp_error($_terms)) {
                    $terms = array();
                    foreach ($_terms as $_term) {
                        $terms[] = '<a href="' . get_admin_url() . 'edit.php?post_type=' . self::$postType . '&' . self::$taxonomy . '=' . $_term->slug . '">' . $_term->name . '</a>';
                    }
                    echo implode(", ", $terms);
                }
            } elseif ($column_name == 'sort-order') {
                echo esc_attr($item->menu_order);
            }
        }

        static function getOptions() {
            if (self::$options === null) {
                self::$options = get_option('nimble-portfolio-config', array());
            }
            return self::$options;
        }

        static function setOptions($options = array()) {
            update_option('nimble-portfolio-config', $options);
            self::$options = $options;
        }

        static function getTaxonomyMeta($term_id, $key) {
            if (!$term_id || !$key) {
                return null;
            }
            $options = self::getOptions();
            return @$options['taxonomymeta'][$term_id][$key];
        }

        static function updateTaxonomyMeta($term_id, $key, $val = null) {
            if (!$term_id || !$key) {
                return;
            }
            $options = self::getOptions();
            $options['taxonomymeta'][$term_id][$key] = $val;
            self::setOptions($options);
        }

        static function taxonomyColumnHeader($columns) {
            $columns["sort-order"] = _e("Sort Order", "nimble_portfolio");
            return $columns;
        }

        static function taxonomyEditFormField() {
            ?>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="sort-order"><?php _e("Sort Order", "nimble_portfolio"); ?></label>
                </th>
                <td>
                    <input type="text" id="sort-order" name="sort-order" value="<?php echo self::getTaxonomyMeta($_GET["tag_ID"], 'sort-order'); ?>" style="width: 100px"/>
                    <p class="description"><?php _e('Set the sort order for your category here with 0 being the first and so on.', 'nimble_portfolio'); ?></p>
                </td>
            </tr>
            <?php
        }

        static function taxonomyAddFormField() {
            ?>
            <div class="form-field">
                <label for="sort-order"><?php _e("Sort Order", "nimble_portfolio"); ?></label>
                <input type="text" id="sort-order" name="sort-order" style="width: 100px"/>
                <p><?php _e('Set the sort order for your category here with 0 being the first and so on.', 'nimble_portfolio'); ?></p>
            </div>        
            <?php
        }

        static function saveTaxonomyValue($term_id) {
            if (isset($_POST['sort-order']))
                self::updateTaxonomyMeta($term_id, 'sort-order', $_POST['sort-order']);
        }

        static function taxonomyCustomValue($empty, $custom_column, $term_id) {

            if ($custom_column == 'sort-order') {
                return self::getTaxonomyMeta($term_id, $custom_column);
            }
        }

        static function taxonomyQuickEditField($column_name, $screen, $name = null) {
            if ($column_name == 'sort-order') {
                ?>  
                <fieldset>  
                    <div id="my-custom-content" class="inline-edit-col">  
                        <label>  
                            <span class="title"><?php _e("Sort Order", "nimble_portfolio"); ?></span>  
                            <span class="input-text-wrap"><input id="nimble-portfolio-sort-order" name="<?php echo $column_name; ?>" class="ptitle" value="" type="text"></span>  
                        </label>  
                    </div>  
                </fieldset>  
                <?php
            }
        }
        
        static public function nimble_portfolio_textdomain() {
			load_plugin_textdomain( 'nimble_portfolio', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
		}
		
		static function validate_loader_settings_before($plugin_options, $redux_options) {
			
			global $nimble_portfolio_configuration_loader;	
			$nimble_portfolio_configuration_loader["color"] = $plugin_options['global_settings_loader_color'];
			$nimble_portfolio_configuration_loader["size"] = $plugin_options['global_settings_loader_size'];
			return $plugin_options;
		}
		
		static function validate_loader_settings_after($field, $value, $prev) {
			
			if  ($field['id'] == 'global_settings_loader_flag' && $value != $prev && $value){

			 try {
					global $nimble_portfolio_configuration_loader;
	                require_once("includes/class.NimblePortfolioLessC.php");
					$less = new NimblePortfolioLessC();
					$less->setVariables(array(
						"loader_color" => $nimble_portfolio_configuration_loader['color'],
						"loader_size" => $nimble_portfolio_configuration_loader['size']
					));
					if ($less->compileFile(self::getPath("includes") . "nimble-portfolio.less", get_template_directory() . "/nimble-portfolio/nimble-portfolio.css") === false) {
						$field['msg'] = "<br /><strong><em>" . get_template_directory() . "/nimble-portfolio/nimble-portfolio.css</em></strong> is not writtable! Loader color won't be saved.";
						$return['error'] = $field;
						$return['value'] = $prev;
						return $return;			
					}
				} catch (Exception $e) {
					$field['msg'] = "<strong>LESS Compiler:</strong> " . $e->getMessage();
					$return['error'] = $field;
					$return['value'] = $prev;
					return $return;			
				} 
			}

		}

    }

    NimblePortfolioPlugin::init();

    function nimble_portfolio($atts = array()) {
        return NimblePortfolioPlugin::getPortfolio($atts);
    }

    function nimble_portfolio_show($atts = array()) {
        NimblePortfolioPlugin::showPortfolio($atts);
    }

}
include("skins/default/skin.php"); // Includes default skin



