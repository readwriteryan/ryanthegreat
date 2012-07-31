<?php
namespace miranda;
require_once(__DIR__ . '/config/Config.php');
require_once(__DIR__ . '/autoloader/Autoloader.php');
require_once(__DIR__ . '/functions/Application.php');

use miranda\autoloader\Autoloader;
use miranda\exceptions\GeneralException;
use miranda\plugins\Session;

Autoloader::registerNamespace('miranda', __DIR__);
Autoloader::registerNamespace('ryanthegreat', '/srv/http/ryanthegreat/application/');

set_exception_handler('ryanthegreat\controllers\Pages::handleException');
require_once(__DIR__ . '/plugins/Session.php');
?>