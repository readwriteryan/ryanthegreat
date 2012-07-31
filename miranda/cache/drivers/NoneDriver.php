<?php
namespace miranda\cache\drivers;
use miranda\cache\CacheInterface;

class NoneDriver implements CacheInterface
{
	public function __construct()
	{
	    
	}
	
	public function get($key)
	{
	    return false;
	}
	
	public function set($key, $value, $expire)
	{
	    return true;
	}
	
	public function add($key, $value, $expire)
	{
	    return true;
	}
	
	public function delete($key)
	{
	    return true;
	}
	
	public function flush()
	{
	    return true;
	}
	
	public function inc($key, $amount = 1)
	{
	    return true;
	}
	
	public function dec($key, $amount = 1)
	{
	    return true;
	}
}
?>