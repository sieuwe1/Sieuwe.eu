<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'ReduxFramework_select_extended' ) ) {
    class ReduxFramework_select_extended {

        /**
         * Field Constructor.
         * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
         *
         * @since ReduxFramework 1.0.0
         */
        public function __construct( $field = array(), $value = '', $parent ) {
            $this->parent = $parent;
            $this->field  = $field;
            $this->value  = $value;
                        
		}


        /**
         * Field Render Function.
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since ReduxFramework 1.0.0
         */
        public function render() {
			global $nimble_portfolio_configuration;
			global $generate_shortcode_page;
			
            $sortable = ( isset( $this->field['sortable'] ) && $this->field['sortable'] ) ? ' select2-sortable"' : "";

            if ( ! empty( $sortable ) ) { // Dummy proofing  :P
                $this->field['multi'] = true;
            }

            if ( ! empty( $this->field['data'] ) && empty( $this->field['options'] ) ) {
                if ( empty( $this->field['args'] ) ) {
                    $this->field['args'] = array();
                }
                
                if ( $this->field['data'] == "genericons" ) {
					
					$icons_file = ReduxFramework::$_dir . 'inc/extensions/'. $this->field['type'] . '/' . $this->field['type'] .'/genericons.php';
                    /**
                     * filter 'redux-font-icons-file}'
                     *
                     * @param  array $icon_file File for the icons
                     */
                    $icons_file = apply_filters( 'redux-font-icons-file', $icons_file );

                    /**
                     * filter 'redux/{opt_name}/field/font/icons/file'
                     *
                     * @param  array $icon_file File for the icons
                     */
                    $icons_file = apply_filters( "redux/{$this->parent->args['opt_name']}/field/font/icons/file", $icons_file );
                    if ( file_exists( $icons_file ) ) {
                        require_once $icons_file;
                    }
                    
                    $this->field['options'] = $genericons;
                    $this->field['class'] .= "nimble-icons-list genericons";
                }
                
				if ( $this->field['data'] !== "genericons" ) {
				
					if($this->field['data'] == 'nimble_portfolio_taxonomy'){
						$generate_shortcode_page['post_type'] = 'portfolio';
						$tax_obj = get_object_taxonomies($generate_shortcode_page['post_type'],'objects');

						if($tax_obj){
							foreach($tax_obj as $key => $obj){
								$this->field['options'][$key] = $obj->label." ($key)"; 
							}
						}else{
							$this->field['options'][0] = __('No Taxnomy Found under the Post Type!','');
						}
						
					}else{
						if($this->field['data'] == 'post_types_shortcode'){
							$wp_post_types = array();
							foreach(get_post_types(array('public' => 'true')) as $post_type){
								$wp_post_types[$post_type] = get_post_type_object($post_type)->labels->name." ($post_type)";
							}
							$this->field['options'] = $wp_post_types;
						}else if($this->field['data'] == 'nimble_portfolio_taxonomy_terms'){
							$generate_shortcode_page['taxonomies'] = 'nimble-portfolio-type';
							$taxonomy_terms = get_terms( $generate_shortcode_page['taxonomies'] , array( 'hide_empty' => false, ) );
							if($taxonomy_terms){
								foreach($taxonomy_terms as $taxonomy){
									$this->field['options'][$taxonomy->term_id] = $taxonomy->name;
								}						
							}
						}else{
							$this->field['options'] = $this->parent->get_wordpress_data( $this->field['data'], $this->field['args'] );
						}
					}
                
				}
                
                if ( $this->field['data'] == "nimble_portfolio_skin" ) {
					$skins = apply_filters('nimble_portfolio_skin_register', array());
					foreach($skins as $skin){
						$this->field['options'][$skin->name] = $skin->label;
					}
				}
				
				if ( $this->field['data'] == "nimble_portfolio_lightbox" ) {
					$lightboxes = apply_filters('nimble_portfolio_lightbox_register', array());
					foreach($lightboxes as $lightbox){
						$this->field['options'][$lightbox['name']] = $lightbox['label'];
					}
				} 
				             
            }

			$multi = ( isset( $this->field['multi'] ) && $this->field['multi'] ) ? ' multiple="multiple"' : "";

			if ( ! empty( $this->field['width'] ) ) {
				$width = ' style="' . $this->field['width'] . '"';
			} else {
				$width = ' style="width: 40%;"';
			}

			$nameBrackets = "";
			if ( ! empty( $multi ) ) {
				$nameBrackets = "[]";
			}

			$placeholder = ( isset( $this->field['placeholder'] ) ) ? esc_attr( $this->field['placeholder'] ) : __( 'Select an item', 'redux-framework' );

			if ( isset( $this->field['select2'] ) ) { // if there are any let's pass them to js
				$select2_params = json_encode( $this->field['select2'] );
				$select2_params = htmlspecialchars( $select2_params, ENT_QUOTES );

				echo '<input type="hidden" class="select2_params" value="' . $select2_params . '">';
			}

            if ( ! empty( $this->field['options'] ) ) {               

                if ( isset( $this->field['multi'] ) && $this->field['multi'] && isset( $this->field['sortable'] ) && $this->field['sortable'] && ! empty( $this->value ) && is_array( $this->value ) ) {
                    $origOption             = $this->field['options'];
                    $this->field['options'] = array();

                    foreach ( $this->value as $value ) {
                        $this->field['options'][ $value ] = $origOption[ $value ];
                    }

                    if ( count( $this->field['options'] ) < count( $origOption ) ) {
                        foreach ( $origOption as $key => $value ) {
                            if ( ! in_array( $key, $this->field['options'] ) ) {
                                $this->field['options'][ $key ] = $value;
                            }
                        }
                    }
                }

                $sortable = ( isset( $this->field['sortable'] ) && $this->field['sortable'] ) ? ' select2-sortable"' : "";
				
				if(isset($this->field['data']) && $this->field['data'] == 'genericons'){
					echo '<select ' . $multi . ' id="' . $this->field['id'] . '-select" data-placeholder="' . $placeholder . '" name="' . $this->field['name'] . $this->field['name_suffix'] . $nameBrackets . '" class="' . $this->field['class'] . $sortable . '"' . $width . ' rows="6" data-tags="true">';
					echo '<option></option>';
				}else{
					echo '<select ' . $multi . ' id="' . $this->field['id'] . '-select" data-placeholder="' . $placeholder . '" name="' . $this->field['name'] . $this->field['name_suffix'] . $nameBrackets . '" class="redux-select-item ' . $this->field['class'] . $sortable . '"' . $width . ' rows="6">';
					echo '<option></option>';
				}

                foreach ( $this->field['options'] as $k => $v ) {

                    if (is_array($v)) {
                        echo '<optgroup label="' . $k . '">';

                        foreach($v as $opt => $val) {
                            $this->make_option($opt, $val, $k);
                        }

                        echo '</optgroup>';

                        continue;
                    }
		
                    $this->make_option($k, $v);
                }
                //foreach
				
				
				
                echo '</select>';
            } else {
				if($this->field['data'] == 'nimble_portfolio_taxonomy_terms'){
					echo '<select ' . $multi . ' id="' . $this->field['id'] . '-select" data-placeholder="' . $placeholder . '" name="' . $this->field['name'] . $this->field['name_suffix'] . $nameBrackets . '" class="redux-select-item ' . $this->field['class'] . '"' . $width . ' rows="6">0</select>';
				}else{
					echo '<strong>' . __( 'No items of this type were found.', 'redux-framework' ) . '</strong>';
				}
            }
        } //function

        private function make_option($id, $value, $group_name = '') {
            if ( is_array( $this->value ) ) {
                $selected = ( is_array( $this->value ) && in_array( $id, $this->value ) ) ? ' selected="selected"' : '';
            } else {
                $selected = selected( $this->value, $id, false );
            }

            echo '<option value="' . $id . '"' . $selected . '>' . $value . '</option>';                
        }

		public function enqueue() {
			
            wp_enqueue_script(
                'redux-field-select-extended-js',
                ReduxFramework::$_url . 'inc/extensions/'.$this->field['type']. '/'. $this->field['type'].'/field_' . $this->field['type'] . Redux_Functions::isMin() . '.js',
                array( 'jquery', 'select2-js', 'redux-js' ),
                time(),
                true
            );
            
        } //function

    } //class
}
