<?php
require_once __DIR__."/../config.php";
require_once SITE_ROOT. "/controllers/SearchController.php";
$search = new SearchController();
$src = "https://edition.cnn.com/";
$links = $search->followLinks($src, $src);
$crawledLinks = $search->returnCrawledLinks();
//$titles = $search->followTitleLinks($src);
//$images = $search->followImageLinks($src);


array_shift($crawledLinks);
foreach($crawledLinks as $site){
    $links = $search->followLinks($site, $src);
    //$titles = $search->followTitleLinks($site);
    //$images = $search->followImageLinks($site);
}

