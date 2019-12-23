<?php
$skin = $this->getSkin();
$skin_options = $skin->getOptions();
 
$readmore_flag = $skin_options['readmore-flag'];
$readmore_text = $skin_options['readmore-text'];
$viewproject_flag = $skin_options['viewproject-flag'];
$viewproject_text = $skin_options['viewproject-text'];
$hover_icon = $skin_options['hover-icon'];
$thumb_size = isset($skin_options['thumb-size']) ? $skin_options['thumb-size'] : '480x480';
$skin_type = isset($this->atts['skin_style']) && $this->atts['skin_style'] ? $this->atts['skin_style'] : '-normal';
$skin_cols = isset($this->atts['skin_columns']) && $this->atts['skin_columns'] ? $this->atts['skin_columns'] : '-columns3';
$title_position = $skin_options['title-position'];
$items = $this->getItems();
foreach ($items as $item) {
    $sep_gal_imgs = array();
    if ($item->getData('sep-gal-ids')) {
        $ids = explode(",", $item->getData('sep-gal-ids'));
        $sep_gal_imgs = $skin->getSepGalImages($ids);
    }
    $item_atts = array();
    $item_atts['class'] = $item->getFilters($this->taxonomy);
    $item_atts['class'][] = "-item";
    $item_atts['class'][] = $skin_type;
    $item_atts['class'][] = $skin_cols;
    $item_atts['id'] = "item-" . $item->ID;
    $item_atts = apply_filters('nimble_portfolio_item_atts', $item_atts, $item, $this);
    $item_link = array();
    $item_link['href'] = esc_url($item->getData('nimble-portfolio'));
    if($item->getData('browse-lightbox-url-newtab'))
		$item_link['target'] = "_blank";
	if($item->post_excerpt)
		$item_link['title'] = esc_attr($item->post_excerpt);
    $item_link['data-rel'] = $item->getData('browse-lightbox-url') ? "" : ($item->getData('sep-gal-ids') ? "nimblebox[nimble_portfolio_gal_$item->ID]" : 'nimblebox[nimble_portfolio_gal_pro]');
    $item_link = apply_filters('nimble_portfolio_lightbox_link_atts', $item_link, $item, $this);
    ?>
    <div <?php echo NimblePortfolioPlugin::phpvar2htmlatt($item_atts); ?>>
        <?php if ($title_position == 'before') { ?>
            <div class="title"><?php echo $item->getTitle(); ?></div>    
        <?php } ?>
        <div class="itembox">
            <a <?php echo NimblePortfolioPlugin::phpvar2htmlatt($item_link); ?>>
                <img src="<?php echo $item->getThumbnail($thumb_size); ?>" alt="<?php echo esc_attr($item->getTitle()); ?>" />
                <div class="-mask"> </div>
                <?php if ($title_position == 'inside') { ?>
                    <div class="title"><?php echo $item->getTitle(); ?></div>    
                <?php } ?>
                <div class="genericon genericon-<?php echo $item->getData('hover-icon') ? $item->getData('hover-icon') : $hover_icon; ?>"></div>
            </a>    
            <?php foreach ($sep_gal_imgs as $img) { ?>
                <a href="<?php echo esc_url($img['url']); ?>" class="sep-gal-img" data-rel="<?php echo "nimblebox[nimble_portfolio_gal_$item->ID]"; ?>" title="<?php echo esc_attr($img['caption']); ?>"><img src="<?php echo $img['url']; ?>" alt="<?php echo esc_attr($item->getTitle()); ?>" /></a>
            <?php } ?>
        </div>
        <?php if ($title_position == 'after') { ?>
            <div class="title"><?php echo $item->getTitle(); ?></div>    
        <?php } ?>
        <?php if ($readmore_flag || $viewproject_flag) { ?> 
            <div class="-links">
                <?php if ($readmore_flag) { ?>
                    <div class="-link -readmore <?php echo $viewproject_flag ? '' : '-onlyonelink'; ?>">
                        <a href="<?php echo $item->getPermalink(); ?>" class="button-fixed">
                            <?php _e($readmore_text, 'nimble_portfolio') ?>
                        </a>
                    </div>
                <?php } ?>
                <?php if ($viewproject_flag) { ?>
                    <div class="-link -viewproject <?php echo $readmore_flag ? '' : '-onlyonelink'; ?>"> 
                        <a href="<?php echo $item->getData('nimble-portfolio-url'); ?>" class="button-fixed">
                            <?php _e($viewproject_text, 'nimble_portfolio') ?>
                        </a>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
    <?php
}
?>
