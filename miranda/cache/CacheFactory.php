<?php
namespace miranda\cache;

use miranda\config\Config;

class CacheFactory
{
    private static $instances = array();
    
    protected function __construct() {}
    
    public static function getInstance($cacheDriver = NULL)
    {
	if(!$cacheDriver) $cacheDriver = Config::get('cache', 'default') ? Config::get('cache', 'default') : 'none';
	
	$driver = 'miranda\\cache\\drivers\\' . ucfirst($cacheDriver) . 'Driver';
		
	if(!isset($instances[$cacheDriver]))
	    $instances[$cacheDriver] = new $driver;
			
	return $instances[$cacheDriver];
    }
}
?>