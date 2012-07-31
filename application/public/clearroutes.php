<?php
require_once(__DIR__ . '/../../miranda/miranda.php');

use miranda\cache\CacheFactory;
$cache = CacheFactory::getInstance(CACHE_DEFAULT);
$cache -> flush();
?>