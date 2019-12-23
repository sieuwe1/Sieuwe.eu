<?php

if (class_exists('NimblePortfolioSkin') && !class_exists('NimblePortfolioSkinDefault')) {

    class NimblePortfolioSkinDefault extends NimblePortfolioSkin {
		
		private $version;

        function __construct() {
			$this->version = NimblePortfolioPlugin::getVersion();
            $skin_default = array();
            $skin_default['title-position'] = 'before';
            $skin_default['readmore-flag'] = 1;
            $skin_default['readmore-text'] = "Read More &rarr;";
            $skin_default['viewproject-flag'] = 1;
            $skin_default['viewproject-text'] = "View Project &rarr;";
            $skin_default['hover-icon'] = 'zoom';
            $skin_default['thumb-size'] = '480x480';
            parent::__construct('default', 'Default', dirname(__FILE__) . '/', $skin_default);
            remove_action('nimble_portfolio_create_section_before',array($this,'registerMenu'));
            add_action('admin_head', array($this, 'admin_head'));
            add_action('nimble_portfolio_tinymce_skin_change', array($this, 'addTinymceShortcodeParams'));
            add_action('nimble_portfolio_shortcode_skin_change', array($this, 'addTinymceShortcodeParams'));
            add_action('nimble_portfolio_renderoptions_field', array($this, 'addItemFields'));
            add_filter('nimble_portfolio_update_data', array($this, 'updateData'));
            add_filter('nimble_portfolio_query_args', array($this, 'setQueryArgs'), 10, 2);
            add_action('nimble_portfolio_create_section_after', array($this, 'adminItemOptions'));
            add_action('wp_ajax_sep_gal_thumbs', array($this, 'ajaxSepGalThumbs'));
            add_action('nimble_portfolio_skin_after', array($this, 'insertPaging'));
            add_action('nimble-portfolio-template-css', array($this, 'insertOverridesCSS'));
        }
		
		function insertOverridesCSS($portfolioObj) {
			$skin_options = $this->getOptions();
			
			if (is_writable("$this->path/overrides.css")) {
				ob_start();
				include 'overrides.php';
				$buffer = ob_get_clean();
				file_put_contents("$this->path/overrides.css", $buffer);
				$skin_options['overrides-written'] = true;
			} else {
				$skin_options['overrides-written'] = false;
			}
			
			if (isset($skin_options['overrides-writable']) && $skin_options['overrides-writable']) {
				?>
				<link rel="stylesheet" type="text/css" href="<?php echo $this->url . "/overrides.css"; ?>" />
				<?php
			} else {
				echo "<style>";
				include 'overrides.php';
				echo "</style>";
			}
        }
		
        function admin_head() {
            wp_enqueue_script('jquery-ui-tabs');
            wp_enqueue_script('farbtastic');
            wp_enqueue_style('farbtastic');
            wp_enqueue_style('genericons-css', $this->url . "/genericon/genericons.css");
            wp_enqueue_style('nimble_portfolio_default_skin_admin', $this->url . "/admin.css", array('farbtastic'));
            wp_enqueue_script('nimble_portfolio_default_skin_admin', $this->url . "/admin.js", array('jquery', 'farbtastic'), $this->version);
        }
        
        function addTinymceShortcodeParams($skin_name) {
			?>
			<fieldset>
				<legend><?php _e('Default Skin','nimble_portfolio'); ?></legend>
				<p>
					<label for="default_skin_filters"><?php _e("Filters: (no selection means ALL)", 'nimble_portfolio'); ?></label>
					<br />
					<?php
					$args = array();
					$args['id'] = 'default_skin_filters';
					$args['taxonomy'] = $_GET['taxonomy'];
					$args['hierarchical'] = 1;
					$args['orderby'] = 'name';
					wp_dropdown_categories($args);
					?>
				</p>
				<p>
					<label for="default_skin_column_type"><?php _e("Skin Columns"); ?>:</label>
					<select id="default_skin_column_type">
						<option value="-columns2"><?php _e('2 Columns','nimble_portfolio'); ?></option>
						<option value="-columns3"><?php _e('3 Columns','nimble_portfolio'); ?></option>
						<option value="-columns4"><?php _e('4 Columns','nimble_portfolio'); ?></option>
						<option value="-columns5"><?php _e('5 Columns','nimble_portfolio'); ?></option>
					</select> 
				</p>
				<p>
					<label for="default_skin_style"><?php _e("Skin Style"); ?>:</label>
					<select id="default_skin_style">
						<option value="-normal"><?php _e('Normal','nimble_portfolio'); ?></option>
						<option value="-round"><?php _e('Round','nimble_portfolio'); ?></option>
						<option value="-square"><?php _e('Square','nimble_portfolio'); ?></option>
					</select> 
				</p>
				<p>
					<label for="items_per_page"><?php _e("Items per page (enables pagination)"); ?>:</label>
					<input type="text" id="items_per_page" />
				</p>
			</fieldset>
			<script>
				(function($) {
					$(document).ready(function() {
						$("#default_skin_filters").attr('multiple', 'multiple');
						$("#default_skin_filters option").removeAttr('selected');
						$("#default_skin_filters").css('height', '100px');
					});
					$(document).on("nimble_portfolio_tinymce_param nimble_portfolio_shortcode_param", function(event, obj) {
						var default_skin_filters = $('#default_skin_filters').val();
						var default_skin_column_type = $('#default_skin_column_type').val();
						var default_skin_style = $('#default_skin_style').val();
						var items_per_page = $('#items_per_page').val();
						var params = event.result ? event.result : '';
						if (default_skin_filters) {
							params += ' filters="' + default_skin_filters + '" ';
						}
						if (default_skin_column_type) {
							params += ' skin_columns="' + default_skin_column_type + '" ';
						}
						if (default_skin_style) {
							params += ' skin_style="' + default_skin_style + '" ';
						}
						if (items_per_page) {
							params += ' items_per_page="' + items_per_page + '" ';
						}
						return params;
					});
				})(jQuery);
			</script>
			<?php
        }

        function getHoverIconSet() {
            return array(
                'gallery' => '&#xf103;',
                'image' => '&#xf102;',
                'show' => '&#xf403;',
                'picture' => '&#xf473;',
                'draggable' => '&#xf436;',
                'video' => '&#xf104;',
                'youtube' => '&#xf213;',
                'vimeo' => '&#xf212;',
                'flickr' => '&#xf211;',
                'instagram' => '&#xf215;',
                'fullscreen' => '&#xf474;',
                'search' => '&#xf400;',
                'zoom' => '&#xf402;',
                'cart' => '&#xf447;',
                'heart' => '&#xf461;',
                'home' => '&#xf409;',
                'category' => '&#xf301;',
                'link' => '&#xf107;',
                'attachment' => '&#xf416;',
                'external' => '&#xf442;',
                'star' => '&#xf408;',
                'key' => '&#xf427;',
            );
        }

        function addItemFields($item) {
            $i = 0;
            $icon_set = $this->getHoverIconSet();
            ?>
            <div class="form-field">
                <label for="browse-lightbox-url"><?php _e('Browse Image/Video URL', 'nimble_portfolio') ?></label>
                <input type="checkbox" name="browse-lightbox-url" id="browse-lightbox-url" value="1" <?php checked($item->getData('browse-lightbox-url'), 1); ?> />
                <p><?php _e('Enabling this check will browse the Image/Video URL instead of displaying in Lightbox', 'nimble_portfolio') ?></p>
            </div>
            <div class="form-field">
                <label for="browse-lightbox-url-newtab"><?php _e('New tab/window for Image/Video URL', 'nimble_portfolio') ?></label>
                <input type="checkbox" name="browse-lightbox-url-newtab" id="browse-lightbox-url-newtab" value="1" <?php checked($item->getData('browse-lightbox-url-newtab'), 1); ?> />
                <p><?php _e('Depends on <strong>Browse Image/Video URL</strong>, opens the URL in new browser tab/window', 'nimble_portfolio') ?></p>
            </div>
            <div class="form-field">
                <label for="hover-icon"><?php _e('Choose hover icon', 'nimble_portfolio') ?></label>
                <select name="hover-icon" id="hover-icon" class="genericon" >
                    <optgroup>
                        <?php foreach ($icon_set as $value => $label) { ?>
                            <option value="<?php echo $value; ?>" <?php selected($item->getData('hover-icon'), $value); ?>><?php echo $label; ?></option>
                            <?php echo ++$i % 4 == 0 ? "</optgroup><optgroup>" : ""; ?>
                        <?php } ?>
                        <option value="" <?php selected($item->getData('hover-icon'), ''); ?>></option>
                    </optgroup>
                </select>
                <p><?php _e('Select hover icon for this item, it will override the icon set in the skin options page', 'nimble_portfolio') ?></p>
            </div>
            <?php
        }

        function updateData($data) {

            if (isset($_POST['hover-icon']) && $_POST['hover-icon']) {
                $data['hover-icon'] = $_POST['hover-icon'];
            }

            if (isset($_POST['browse-lightbox-url']) && $_POST['browse-lightbox-url']) {
                $data['browse-lightbox-url'] = $_POST['browse-lightbox-url'];
            } else {
                $data['browse-lightbox-url'] = 0;
            }

            if (isset($_POST['browse-lightbox-url-newtab']) && $_POST['browse-lightbox-url-newtab']) {
                $data['browse-lightbox-url-newtab'] = $_POST['browse-lightbox-url-newtab'];
            } else {
                $data['browse-lightbox-url-newtab'] = 0;
            }
            if (isset($_POST['sep-gal-ids']) && $_POST['sep-gal-ids']) {
                $data['sep-gal-ids'] = $_POST['sep-gal-ids'];
            } else {
                $data['sep-gal-ids'] = '';
            }

            return $data;
        }

        function setQueryArgs($args, $portfolioObj) {
           $atts = $portfolioObj->atts;
		   if (isset($atts['filters']) && $atts['filters']) {
				$filters_r = explode(",", $atts['filters']);
				sort($filters_r);
				$args['tax_query'] = array('relation' => 'OR',array('taxonomy' => $portfolioObj->taxonomy, 'field' => 'id', 'terms' => $filters_r, 'include_children' => false));
			}
			if (isset($atts['items_per_page']) && $atts['items_per_page']) {
				$args['posts_per_page'] = (int) $atts['items_per_page'];
				$args['paged'] = get_query_var('paged') ? get_query_var('paged') : (get_query_var('page')?get_query_var('page'):1);
			}
			if (isset($atts['pagenum']) && $atts['pagenum']) {
				$args['paged'] = (int) $atts['pagenum'];
			}
            return $args;
        }

        function adminItemOptions() {
            add_meta_box('nimble-portfolio-section-sep-gal', __('Seprate Gallery for this item', 'nimble_portfolio_context'), array($this, 'sepGalSection'), NimblePortfolioPlugin::getPostType(), 'normal', 'high');
        }

        function sepGalSection($post) {
            $item = new NimblePortfolioItem($post->ID);
            $thumbs = array();
            if ($item->getData('sep-gal-ids')) {
                $ids = explode(",", $item->getData('sep-gal-ids'));
                $thumbs = $this->getSepGalImages($ids, '60x60');
            }
            ?>
            <div class="nimble-portfolio-meta-section">
                <div class="form-wrap">
                    <div class="form-field">
                        <input type="hidden" value="<?php echo $item->getData('sep-gal-ids'); ?>" name="sep-gal-ids" id="nimble_portfolio_gal_ids" />
                        <a rel="#nimble_portfolio_gal_ids" class="button" href="javascript:void(0);" id="nimble_portfolio_open_gal"><?php _e('Add/Edit Gallery Images','nimble_portfolio'); ?></a>
                        <h4><?php _e('Separate Gallery Images','nimble_portfolio'); ?></h4>
                        <div id="sep-gal-images">
                            <?php
                            foreach ($thumbs as $thumb) {
                                echo "<img src='" . $thumb['url'] . "' />";
                            }
                            ?>
                        </div>
                        <div id="sep-gal-loader"><img src="<?php echo "$this->url/images/loading.gif"; ?>" /></div>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
            <?php
        }

        function getSepGalImages($ids = array(), $size = ' full') {
            $return = array();
            if (count($ids) > 0) {
                foreach ($ids as $id) {
                    $item = new NimblePortfolioItem($id);
                    $src = $item->getAttachmentSrc($id, $size);
                    $return[] = array('url' => $src[0], 'title' => $item->getTitle(), 'caption' => $item->post_excerpt);
                }
            }
            return $return;
        }

        function ajaxSepGalThumbs($ids = array()) {

            if (isset($_GET['sep-gal-ids']) && $_GET['sep-gal-ids']) {
                $ids = explode(",", $_GET['sep-gal-ids']);
            }
            $thumbs = $this->getSepGalImages($ids, '60x60');
            foreach ($thumbs as $thumb) {
                echo "<img src='" . $thumb['url'] . "' />";
            }
            exit;
        }

        function paginate_links($args) {
            $defaults = array(
                'base' => '%_%', // http://example.com/all_posts.php%_% : %_% is replaced by format (below)
                'format' => '?page=%#%', // ?page=%#% : %#% is replaced by the page number
                'total' => 1,
                'current' => 0,
                'show_all' => false,
                'prev_next' => true,
                'prev_text' => __('&laquo; Previous'),
                'next_text' => __('Next &raquo;'),
                'end_size' => 1,
                'mid_size' => 2
            );

            $args = wp_parse_args($args, $defaults);
            extract($args, EXTR_SKIP);

            $total = (int) $total;
            if ($total < 2)
                return array();
            $current = (int) $current;
            $end_size = 0 < (int) $end_size ? (int) $end_size : 1;
            $mid_size = 0 <= (int) $mid_size ? (int) $mid_size : 2;
            $r = '';
            $page_links = array();
            $n = 0;
            $dots = false;

            if ($prev_next && $current && 1 < $current) :
                $link = str_replace('%_%', 2 == $current ? '' : $format, $base);
                $link = str_replace('%#%', $current - 1, $link);
                $page_links[] = sprintf('<a class="-prev -page-link" href="%s" data-rel="%d">%s</a>', esc_url($link), $current - 1, $prev_text);
            endif;
            for ($n = 1; $n <= $total; $n++) :
                $n_display = number_format_i18n($n);
                if ($n == $current) :
                    $page_links[] = "<span class='-page-link active'>$n_display</span>";
                    $dots = true;
                else :
                    if ($show_all || ( $n <= $end_size || ( $current && $n >= $current - $mid_size && $n <= $current + $mid_size ) || $n > $total - $end_size )) :
                        $link = str_replace('%_%', 1 == $n ? '' : $format, $base);
                        $link = str_replace('%#%', $n, $link);
                        $page_links[] = sprintf('<a class="-page-link" href="%s" data-rel="%d">%s</a>', esc_url($link), $n, $n_display);
                        $dots = true;
                    elseif ($dots && !$show_all) :
                        $page_links[] = '<span class="-page-link dots">' . __('&hellip;') . '</span>';
                        $dots = false;
                    endif;
                endif;
            endfor;
            if ($prev_next && $current && ( $current < $total || -1 == $total )) :
                $link = str_replace('%_%', $format, $base);
                $link = str_replace('%#%', $current + 1, $link);
                $page_links[] = sprintf('<a class="-next -page-link" href="%s" data-rel="%d">%s</a>', esc_url($link), $current + 1, $next_text);
            endif;

            return $page_links;
        }

        function insertPaging($portfolio) {
			if($portfolio->skin == 'default'){
				require 'paging.php';
			}
        }

    }

}

new NimblePortfolioSkinDefault();
