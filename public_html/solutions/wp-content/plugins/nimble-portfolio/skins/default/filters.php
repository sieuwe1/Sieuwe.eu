<?php
$args = array();
if (isset($this->atts['filters']) && $this->atts['filters']) {
    $filters_r = explode(",", $this->atts['filters']);
    sort($filters_r);
    $filters = implode(",", $filters_r);
    $args['include'] = $filters;
    $args['hierarchical'] = 1;
}

$filters = $this->getFilters($args);
foreach ($filters as $filter) {
    $filter_atts = array();
    $filter_atts['href'] = get_term_link($filter->slug, $this->taxonomy);
    $filter_atts['data-rel'] = $filter->slug;
    $filter_atts['class'] = array("-filter");
    $filter_atts['id'] = "filter-" . $filter->term_id;
    $filter_atts = apply_filters('nimble_portfolio_filter_atts', $filter_atts, $filter,$this);
    ?>
    <a <?php echo NimblePortfolioPlugin::phpvar2htmlatt($filter_atts); ?>><?php echo apply_filters('nimble_portfolio_filter_name', $filter->name, $filter); ?></a>
    <?php
}
