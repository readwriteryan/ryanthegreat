<?php
namespace miranda\plugins;
use miranda\orm\ORM;

class Session extends ORM
{
    protected static $table_name	= 'sessions';
    protected static $primary_key	= 'session_id';
    protected static $column_map	= array();
    
    public function __construct()
    {
	$this -> setup();
	$this -> isCacheable(3600);
    }
    
    public function __destruct()
    {
	$this -> session_last_active = time();
	
	/** Remove the flash data if it has not just been set on this page load */
	if(isset($this -> values['session_data']['flash']) && $this -> values['session_data']['flash_set'] == false)
	    unset($this -> values['session_data']['flash']);    
	
	$hash			= crc32(serialize($this));
	$force			= $hash == $this -> self_hash ? false : true;
	$this -> self_hash	= $hash;
	
	$this -> save($force);
    }
    
    public function __get($key)
    {
	if(array_key_exists($key, static::$column_map))
	    return $this -> values[static::$column_map[$key]];
	else
	    return $this -> values['session_data'][$key];
    }
    
    public function __set($key, $value)
    {
	if(array_key_exists($key, static::$column_map))
	{
	    if($key == 'session_data')
		$value = unserialize($value);
		
	    $this -> values[static::$column_map[$key]] = $value;
	    $this -> updated[] = $key;
	}
	else
	{
	    $this -> values['session_data'][$key] = $value;
	    
	    /** Handle special case for setting slash data that should expire after one page load */
	    if($key == 'flash')
		$this -> values['session_data']['flash_set'] = true;
		
	    $this -> updated[] = 'session_data';
	}
    }
    
    private function init()
    {
	$this -> session_id		= uniqid(mt_rand(1000000, 9999999), true);
	$this -> session_ip		= $_SERVER['REMOTE_ADDR'];
	$this -> session_ua 		= $_SERVER['HTTP_USER_AGENT'];
	$this -> values['session_data']	= array();
    }
    
    public function validate()
    {
	$this -> flash_set = false;
	
	if($this -> session_ip != $_SERVER['REMOTE_ADDR'] || $this -> session_ua != $_SERVER['HTTP_USER_AGENT'])
	{
	    $this -> delete();
	    
	    setcookie('miranda_sessionid', '', -3600, '/');
	    return false;
	}
	
	return true;
    }
    
    protected static function setup()
    {
	self::hasColumn('session_id', 'session_id');
	self::hasColumn('session_data', 'session_data');
	self::hasColumn('session_ip', 'session_ip');
	self::hasColumn('session_ua', 'session_ua');
	self::hasColumn('session_last_active', 'session_last_active');
    }
    
    public function getId($regenerate = false)
    {
	if($regenerate)
	{
	    $this -> init();
	    
	    setcookie('miranda_sessionid', $this -> values['session_id'], 0, '/');
	}
	    
	return $this -> session_id;
    }
    
    public function getLastActive()
    {
	return $this -> session_last_active;
    }
}
?>