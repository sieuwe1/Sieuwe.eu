<?php

if (isset($skin_options['title-color']) && $skin_options['title-color'] != '#') {
    echo ".-skin-default .-items .-item .title { color: " . $skin_options['title-color'] . ";}";
}
if (isset($skin_options['title-bgcolor']) && $skin_options['title-bgcolor'] != '#') {
    echo ".-skin-default .-items .-item .title { background-color: " . $skin_options['title-bgcolor'] . ";}";
}
if (isset($skin_options['readmore-color']) && $skin_options['readmore-color'] != '#') {
    echo ".-skin-default .-items .-item .-link.-readmore a { color: " . $skin_options['readmore-color'] . ";}";
}
if (isset($skin_options['viewproject-color']) && $skin_options['viewproject-color'] != '#') {
    echo ".-skin-default .-items .-item .-link.-viewproject a { color: " . $skin_options['viewproject-color'] . ";}";
}
if (isset($skin_options['filter_bg_color']) && $skin_options['filter_bg_color'] != '#') {
    echo ".-skin-default .-filters .-filter, .-skin-default .-pages .-page-link { background-color: " . $skin_options['filter_bg_color'] . ";}";
}
if (isset($skin_options['filter_link_color']) && $skin_options['filter_link_color'] != '#') {
    echo ".-skin-default .-filters .-filter, .-skin-default .-pages .-page-link { color: " . $skin_options['filter_link_color'] . ";}";
}
if (isset($skin_options['filter_border_color']) && $skin_options['filter_border_color'] != '#') {
    echo ".-skin-default .-filters .-filter, .-skin-default .-pages .-page-link { border: 1px solid " . $skin_options['filter_border_color'] . ";}";
}
if (isset($skin_options['filter_bg_color_hover']) && $skin_options['filter_bg_color_hover'] != '#') {
    echo ".-skin-default .-filters .-filter:hover, .-skin-default .-pages .-page-link:hover,.-skin-default .-filters .-filter.active, .-skin-default .-pages .-page-link.active { background-color: " . $skin_options['filter_bg_color_hover'] . ";}";
}
if (isset($skin_options['filter_link_color_hover']) && $skin_options['filter_link_color_hover'] != '#') {
    echo ".-skin-default .-filters .-filter:hover, .-skin-default .-pages .-page-link:hover,.-skin-default .-filters .-filter.active, .-skin-default .-pages .-page-link.active { color: " . $skin_options['filter_link_color_hover'] . ";}";
}
if (isset($skin_options['filter_border_color_hover']) && $skin_options['filter_border_color_hover'] != '#') {
    echo ".-skin-default .-filters .-filter:hover, .-skin-default .-pages .-page-link:hover,.-skin-default .-filters .-filter.active, .-skin-default .-pages .-page-link.active { border: 1px solid " . $skin_options['filter_border_color_hover'] . ";}";
}
if (isset($skin_options['hover-color']) && $skin_options['hover-color'] != '#') {
    echo ".-skin-default .-items .-item .genericon { color: " . $skin_options['hover-color'] . " !important;}";
}

if (isset($skin_options['hover-bgcolor']) && $skin_options['hover-bgcolor'] != '#') {
    echo ".-skin-default .-items .-item .-mask { background-color: " . $skin_options['hover-bgcolor'] . " !important;}";
}


