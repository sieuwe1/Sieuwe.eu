<?php

class NimblePortfolioPrettyPhoto {

	static private $version;
    static private $dirUrl;
    static private $dirPath;
    static private $name;
    static private $label;
    
    static function init() {
        add_action('init', array(__CLASS__, 'setup'));
    }

    static function setup($params = array()) {
        self::$dirUrl = NimblePortfolioPlugin::getUrl('includes/prettyphoto/');
        self::$dirPath = NimblePortfolioPlugin::getPath('includes/prettyphoto/');
        self::$version = NimblePortfolioPlugin::getVersion();
        self::$label = 'PrettyPhoto';
        self::$name = 'prettyphoto';
        
        add_filter('nimble_portfolio_lightbox_register', array(__CLASS__, 'register'));
        add_action('nimble_portfolio_enqueue_script', array(__CLASS__, 'enqueueScript'));
        add_action('nimble_portfolio_enqueue_style', array(__CLASS__, 'enqueueStyle'));
        add_action('wp_head', array(__CLASS__, 'insertAddthisScript'), 99);
    }
	
	static function register($lightboxes) {
		$lightboxes[] = array('label' => self::$label, 'name' => self::$name);
		return $lightboxes;
	}

    static function enqueueScript() {
		$options = self::getFormattedOptions();

		$nimblebox = apply_filters('nimble_portfolio_lightbox_script', self::$dirUrl . "prettyphoto.js");
		if ($nimblebox) {
			wp_enqueue_script('nimblebox-script', $nimblebox, array('jquery'), self::$version);
		}
	
		if($nimblebox == self::$dirUrl . "prettyphoto.js"){
			wp_register_script('nimble-portfolio-prettyPhoto', self::$dirUrl . "nimble-prettyPhoto.js", array('jquery'), self::$version);
			$options['AddThis'] = apply_filters('nimble_portfolio_pp_addthis_params', array('services' => array('facebook', 'twitter', 'pinterest_share', 'compact')));
			$options['wp_url'] = self::$dirUrl;
			wp_localize_script('nimble-portfolio-prettyPhoto', 'NimblePrettyPhoto', json_encode($options));
			wp_enqueue_script('nimble-portfolio-prettyPhoto');
		}
    }
    
    static function enqueueStyle() {
		$nimblebox = apply_filters('nimble_portfolio_lightbox_style', self::$dirUrl . 'prettyphoto.css');
		if ($nimblebox) {
			wp_enqueue_style('nimblebox-style', $nimblebox);
		}
	}

    static function getFormattedOptions($options = null) {

        global $nimble_portfolio_configuration;
        $options_integer = array('slideshow', 'default_width', 'default_height', 'horizontal_padding');
        $options_boolean = array('autoplay_slideshow', 'show_title', 'allow_resize', 'hideflash', 'autoplay', 'modal', 'deeplinking', 'overlay_gallery', 'keyboard_shortcuts', 'download_icon');
		$formatted = array();
		
		foreach($nimble_portfolio_configuration as $key => $option){
			if (strpos($key, 'prettyphoto_') !== false) {
				$index = str_replace('prettyphoto_','',$key);
				if(in_array($index,$options_integer))	$option = $option !=='' ? (int)$option : 0;
				if(in_array($index,$options_boolean))	$option = $option !=='' ? (bool)$option : false;
				if($index == 'opacity')	$option = (float)$option;
				$formatted[$index] = $option;
			}
		}
		                
        return $formatted;
    }

    static function setOptions($options) {
        update_option('nimble-portfolio-prettyPhoto-options', $options);
    }

    static function insertAddthisScript() {
        $options = self::getFormattedOptions();
        if (isset($options['addthis_script']) && $options['addthis_script'] ){
            echo stripcslashes($options['addthis_script']);
        }
    }

}

NimblePortfolioPrettyPhoto::init();
