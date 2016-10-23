<?php

use system\core\Assets;

$assets = [
    "bootstrapcss" => "css/bootstrap.min.css",
    "styles" => "css/styles.css",
    "logo1" => ["images/logo.png", "title= PRESTO MVC class= img-responsive imagen width=130 height=25"],
    "logo2" => ["images/logo.png", "title= PRESTO MVC  width=130 height=25"]
];
Assets::add($assets);
// group by name only extension js, and css.
$js = ["js" => [ "js/jquery.min.js", "js/bootstrap.min.js"]];
Assets::group($js);
