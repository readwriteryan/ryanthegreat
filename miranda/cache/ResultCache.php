<?php
namespace miranda\cache;

use miranda\cache\CacheFactory;
use miranda\config\Config;

class ResultCache
{
	public static function get($key, $closure = NULL, $expire = 0, $driver = NULL)
	{
		if(!$driver) $driver = Config::get('cache', 'default') ? Config::get('cache', 'default') : 'none';
		
		if(!($engine = CacheFactory::getInstance($driver))) return false;
		
		if($value = $engine -> get($key)) return $value;
		else if(is_callable($closure))
		{
			$engine -> set($key, $value = $closure(), $expire);
			return $value;
		}
		
		return false;
	}
	
	public static function set($key, $value, $expire = 0, $closure = NULL, $driver = NULL)
	{
		if(!$driver) $driver = Config::get('cache', 'default') ? Config::get('cache', 'default') : 'none';
		
		if(!($engine = CacheFactory::getInstance($driver))) return false;
		
		if($value) return $engine -> set($key, $value, $expire);
		else if(is_callable($closure))
		{
			$engine -> set($key, $value = $closure(), $expire);
			return $value;
		}
		
		return false;
	}
	
	public static function add($key, $value, $expire = 0, $closure = NULL, $driver = NULL)
	{
		if(!$driver) $driver = Config::get('cache', 'default') ? Config::get('cache', 'default') : 'none';
		
		if(!($engine = CacheFactory::getInstance($driver))) return false;
		
		if($value) return $engine -> add($key, $value, $expire);
		else if(is_callable($closure))
		{
			$engine -> add($key, $value = $closure(), $expire);
			return $value;
		}
		
		return false;
	}
	
	public static function delete($key, $driver = NULL)
	{
		if(!($engine = CacheFactory::getInstance($driver))) return false;
		
		return $engine -> delete($key);
	}
	
	public static function flush($driver = NULL)
	{
		if(!($engine = CacheFactory::getInstance($driver))) return false;
		
		return $engine -> flush();
	}
}
?>