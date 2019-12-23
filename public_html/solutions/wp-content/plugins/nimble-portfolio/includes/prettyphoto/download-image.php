<?php

$img = $_GET['img'];
if ($img && strrpos($img, ".")) {
    $ext = substr($img, strrpos($img, "."));
    switch ($ext) {
        case ".gif": $ctype = "image/gif";
            break;
        case ".png": $ctype = "image/png";
            break;
        case ".jpeg":
        case ".jpg": $ctype = "image/jpg";
            break;
        default: exit;
    }
    
    $img_data = file_get_contents( $img );
    $fsize = strlen($img_data);
    $filename = basename($img);

}


if (ini_get('zlib.output_compression'))
    ini_set('zlib.output_compression', 'Off');

header("Pragma: public"); // required
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private", false); // required for certain browsers
header("Content-Type: $ctype");
header("Content-Disposition: attachment; filename=\"" . basename($img) . "\";");
header("Content-Transfer-Encoding: binary");
header("Content-Length: ".$fsize); 
echo $img_data;

?>