<?php
require_once __DIR__."/../config.php";
require_once SITE_ROOT. "/controllers/SearchController.php";
$search = new SearchController();
$links = $search->getLinksWithNoTitle();


