<?php
namespace miranda\config;

class Config
{
	private static $config = false;
	
	private static function load()
	{
		self::$config = parse_ini_file(__DIR__ . '/config.ini', true); /** Load general miranda configuration */
		self::$config = array_merge(self::$config, parse_ini_file('/srv/http/ryanthegreat/application/config/config.ini', true)); /** Load application configuration and merge */
	}
	
	public static function get($grouping, $key)
	{
		if(!self::$config) self::load();
		
		return isset(self::$config[$grouping][$key]) ? self::$config[$grouping][$key] : false;
	}
}
?>