<?php
namespace miranda\cache\drivers;
use miranda\cache\CacheInterface;

class MemcacheDriver implements CacheInterface
{
    private $server	= 'localhost';
    private $port	= '11211';
    private $engine;
	
    public function __construct($server = NULL, $port = NULL)
    {
	$server	= $server ? $server : $this -> server;
	$port	= $port ? $port : $this -> port;
	
	$memcache = new \Memcache();
	$memcache -> connect($server, $port);
		
	$this -> engine = $memcache;
    }
	
	public function __cal()
	{
	    $arguments = function_get_args();
	    return call_user_func_array($this -> engine, $arguments);
	}
	
	public function get($key)
	{
	    return $this -> engine -> get($key);
	}
	
	public function set($key, $value, $expire)
	{
	    return $this -> engine -> set($key, $value, false, $expire);
	}
	
	public function add($key, $value, $expire)
	{
	    return $this -> engine -> add($key, $value, false, $expire);
	}
	
	public function delete($key)
	{
	    return $this -> engine -> delete($key);
	}
	
	public function flush()
	{
	    return $this -> engine -> flush();
	}
	
	public function inc($key, $amount = 1)
	{
	    return $this -> engine -> increment($key, $amount);
	}
	
	public function dec($key, $amount = 1)
	{
	    return $this -> engine -> decrement($key, $amount);
	}
}
?>