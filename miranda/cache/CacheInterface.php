<?php
namespace miranda\cache;

Interface CacheInterface
{    
    public function __construct();
    
    /**
    *	Gets a key/value pair from the cache
    *	
    *	@param	- key: string, the key for which to retrieve the cache
    *	@return	- mixed, the value stored at $key or false if the value does not exist
    */
    public function get($key);
    
    /**
    *	Sets a key/value pair in the cache
    *	
    *	@param	- key: string, the key at which to store the data
    *	@param	- value: mixed, the value to store at key $key
    *	@param	- expires, int, the expire time in seconds for the data
    *	@return	- boolean, the status of the operation: true for success false on failure.
    */
    public function set($key, $value, $expire);
    
    /**
    *	Sets a key/value pair in the cache as long as it doesnt already exist
    *	
    *	@param	- key: string, the key at which to store the data
    *	@param	- value: mixed, the value to store at key $key
    *	@param	- expires, int, the expire time in seconds for the data
    *	@return	- boolean, the status of the operation: true for success false on failure.
    */
    public function add($key, $value, $expire);
    
    /**
    *	Removes a key/value pair from cache
    *	
    *	@param	- key: string, the key at which to remove the data
    *	@return	- boolean, the status of the operation: true on success, false on failure.
    */
    public function delete($key);
    
    /**
    *	Removes all key/value pairs from the cache
    *	
    *	@return	- boolean, the status of the operation: true on success, false on failure.
    */
    public function flush();
    
    /**
    *	Increments a value stored in the cache
    *	
    *	@param	- key: string, the key at which to increment the value
    *	@param	- amount: int, the amount by which to increment the value stored at $key (defaults to 1)
    *	@return	- int, the new value stored at $key or false if the operation was unsucessful
    */
    public function inc($key, $amount = 1);
    
    /**
    *	Decrements a value stored in the cache
    *	
    *	@param	- key: string, the key at which to decrement the value
    *	@param	- amount: int, the amount by which to decrement the value stored at $key (defaults to 1)
    *	@return	- int, the new value stored at $key or false if the operation was unsucessful
    */
    public function dec($key, $amount = 1);
    
}
?>