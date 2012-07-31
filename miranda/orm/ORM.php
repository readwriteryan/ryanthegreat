<?php
namespace miranda\ORM;

use miranda\database\PDOEngine;
use miranda\cache\CacheFactory;
use miranda\exceptions\GeneralException;
use miranda\plugins\Pagination;
use miranda\config\Config;
use PDO;

class ORM
{
    protected static $table_name	= NULL;
    protected static $primary_key	= NULL;
    protected static $column_map	= [];
    protected static $accessible	= true;
    protected $persisted		= false;
    protected $values			= [];
    protected $updated			= false;
    protected $cacheable		= false;
    protected $cache_expire		= 0;
    protected $foreign_key		= NULL;
    protected $functions		= [];
    
    protected function __construct() {} /** Should not be instantiated directly */
    
    
    public function __get($key)
    {
	if(array_key_exists($key, static::$column_map))
	{
	    $key = static::$column_map[$key];
	}
	
	if(!is_array(static::$accessible) || array_key_exists($key, static::$accessible))
	    return isset($this -> values[$key]) ? $this -> values[$key] : NULL;
	else
	    throw new GeneralException("Property: '$key' is inaccessible.");
    }
    
    public function __set($key, $value)
    {
	if(array_key_exists($key, static::$column_map))
	{
	    $key = static::$column_map[$key];
	}
	
	if(!is_array(static::$accessible) || array_key_exists($key, static::$accessible))
	{
	    $this -> updated[]		= $key;
	    $this -> values[$key]	= $value;;
	}
	else
	    throw new GeneralException("Property: '$key' is inaccessible.");
    }
    
    /** Overloads the calling of object finding methods in an object context */
    public function __call($method, $arguments)
    {
	$valid = ['find', 'findOne', 'findBy', 'where'];	
	
	if(in_array($method, $valid))
	{
	    array_unshift($arguments, $this);
	    
	    return call_user_func_array("static::$method", $arguments);
	}
	else if(array_key_exists($method, $this -> functions))
	    return call_user_func_array($method, $arguments);
	else
	    return false;
    }
    
    /** Overloads the calling of object finding methods in a static context */
    public static function __callStatic($method, $arguments)
    {
	$valid = ['find', 'findOne', 'findBy', 'where'];
	
	array_unshift($arguments, NULL);
	
	if(in_array($method, $valid))
	    return call_user_func_array("static::$method", $arguments);
	else
	    return false;
    }
    
    /** Assigns a table mapping for a given model */
    public function mapsTo($table_name)
    {
	static::$table_name = $table_name;
    }
    
    /** Establishes a colump mapping on a given model */
    public static function hasColumn($column_name, $column_map = NULL)
    {
	if(!$column_map) $column_map = $column_name;
	
	static::$column_map[$column_name] = $column_map;
    }
    
    /** Establishes a relationship between two models */
    public function hasRelationship($relationship_alias, $relationship_class, $foreign_key)
    {
	$this -> values[$relationship_alias] = new $relationship_class;
	$this -> values[$relationship_alias] -> $foreign_key = isset($this -> values[static::$column_map[static::$primary_key]]) ? $this -> values[static::$column_map[static::$primary_key]] : 0;
	$this -> values[$relationship_alias] -> setForeignKey($foreign_key);
    }
    
    /** Sets a property on a model as accessible, doing so will make properties which have not been declared accessible inaccessible */
    public function accessible($key)
    {
	static::$accessible[$key] = true;
    }
    
    public function getForeignKey()
    {
	return $this -> foreign_key;
    }
    
    public function setForeignKey($foreign_key)
    {
	$this -> foreign_key = $foreign_key;
    }
	
    public function isCacheable($expireTime)
    {
	$this -> cacheable	= true;
	$this -> cache_expire	= (int) intval($expireTime);
    }
    
    public function clearUpdated()
    {
	$this -> updated = array();
    }
    
    public function getNew()
    {		
	$new = new static;
	if(!empty($this -> foreign_key) && !empty($this -> values[static::$column_map[$this -> foreign_key]]))
	{
	    $foreign_key = $this -> foreign_key;
	    $new -> setForeignKey($foreign_key);
	    $new -> $foreign_key = $this -> values[static::$column_map[$this -> foreign_key]];
	}
		
	return $new;
    }
    
    public function escape()
    {
	foreach($this -> values as $key => $value)
	    if(is_string($value)) $this -> values[$key] = htmlentities($value, ENT_QUOTES, Config::get('site', 'charset'));
	    
	return $this;
    }
    
    protected function update($force = false)
    {
	$db = PDOEngine::getInstance();
	if(!$db) return false;
	
	$query	= 'UPDATE `' . static::$table_name .'` SET ';
	$values	= array();
		
	if(!is_array($this -> updated) && !$force)
	{
	    return false;
	}
	else if(is_array($this -> updated))
	{
	    foreach($this -> updated as $key)
	    {
		$query	= $query . static::$column_map[$key] . ' = ?, ';
		$values[]	= is_array($this -> values[static::$column_map[$key]]) ? serialize($this -> values[static::$column_map[$key]]) : $this -> values[static::$column_map[$key]];
	    }
	}
	else
	{
	    foreach(static::$column_map as $map_key => $value_key)
	    {
		$query	.= '?, ';
		$values[]	= isset($this -> values[$value_key]) ? is_array($this -> values[$value_key]) ? serialize($this -> values[$value_key]) : $this -> values[$value_key] : '';
	    }
	}
	
	
			
	$query = substr($query, 0, -2);
	$query .= ' WHERE `' . static::$primary_key . "` = '{$this -> values[static::$column_map[static::$primary_key]]}'";
	
	$stmt	= $db -> prepare($query);
	$result	= $stmt -> execute($values);
	$stmt -> closeCursor();
		
	$this -> updated = false;
	
	return $result;
    }
		
    protected function insert()
    {
	if(!isset($this -> values[self::$primary_key])) $this -> values[self::$primary_key] = 0;
		
	$db = PDOEngine::getInstance();
	if(!$db) return false;
	
	$query	= 'INSERT INTO `' . static::$table_name .'` ( ' . implode(array_keys(static::$column_map), ',') . ') VALUES (';
	$values	= array();
		
	foreach(static::$column_map as $map_key => $value_key)
	{
	    $query	.= '?, ';
	    $values[]	= isset($this -> values[$value_key]) ? is_array($this -> values[$value_key]) ? serialize($this -> values[$value_key]) : $this -> values[$value_key] : '';
	}
	
	$query	= substr($query, 0, -2) . ')';
	$stmt	= $db -> prepare($query);
	
	$result = $stmt -> execute($values);
	
	$this -> values[static::$primary_key] = $db -> lastInsertId();
	$stmt -> closeCursor();
	$this -> persisted = true;
	
	return $result;
    }
    
    protected static function findOne($refobject = NULL, $pkey = 0)
    {		
	if(isset($refobject) && ($foreign_key = $refobject -> getForeignKey()) != NULL)
	{
	    $use_foreign_key	= true;
	    $foreign_key_value	= $refobject -> $foreign_key;
	}
	else
	{
	    $use_foreign_key = false;
	    static::setup();
	}
		
	$cache = CacheFactory::getInstance();
	if($object = $cache -> get(static::$table_name . '_' . $pkey)) return $object;

	$db	= PDOEngine::getInstance();
	$query	= "SELECT ";
	$keys 	= implode(',', array_keys(static::$column_map));
		
	$query .= $keys . ' FROM `' . static::$table_name . '` ';
		
	if($pkey)
	{
	    $query .= 'WHERE `' . static::$primary_key . '` = :pkey ';
	}
	
	if($use_foreign_key)
	    $query .= "&& `$foreign_key` = :fkey "; 
		
	$query .= 'LIMIT 1';
	$stmt = $db -> prepare($query);
		
	if($pkey)
	    $stmt -> bindParam(':pkey', $pkey);
	    
	if($use_foreign_key)
	    $stmt -> bindParam(':fkey', $foreign_key_value);
	
	$stmt -> execute();
	
	    
	$stmt -> setFetchMode(PDO::FETCH_CLASS, get_called_class());
	$object = $stmt -> fetch();
	
	if(!$object)
	    return false;
	    
	$object -> persisted = true;
	    
	$object -> clearUpdated();
	$stmt -> closeCursor();
	
	return $object;
    }
    
    protected static function find($refobject = NULL, $limit = 0, $order_by = '', $sort_order = 'ASC')
    {
	/** Check context in which we were called */
	if(isset($refobject) && ($foreign_key = $refobject -> getForeignKey()) != NULL)
	{
	    $use_foreign_key	= true;
	    $foreign_key_value	= $refobject -> $foreign_key;
	}
	else
	{
	    $use_foreign_key = false;
	    static::setup();
	}
	
	$db	= PDOEngine::getInstance();
	$query	= "SELECT ";
	$keys 	= implode(',', array_keys(static::$column_map));
		
	$query .= $keys . ' FROM `' . static::$table_name . '`';
	
	if($use_foreign_key)
	    $query .= " WHERE $foreign_key = :fkey";
	    
	if(!empty($order_by) && isset(static::$column_map[$order_by]))
	{
	    if($sort_order != 'ASC')
	    {
		$sort_order = 'DESC';
	    }
			
	    $query .= " ORDER BY `$order_by` $sort_order";
	}
	
	if($limit)
	{
	    if(!is_numeric($limit))
	    {
		list($start, $amount) = array_map('intval', explode(',', $limit));
		$query .= " LIMIT $start,$amount";
	    }
	    else
	    {
		$query .= "LIMIT $limit";
	    }
	}
		
	$stmt = $db -> prepare($query);

	if($use_foreign_key)
	    $stmt -> bindParam(':fkey', $foreign_key_value);
		
	$found	= array();
		
	$stmt -> execute();
	$stmt -> setFetchMode(PDO::FETCH_CLASS, get_called_class());
		
	while($object = $stmt -> fetch())
	{
	    $object -> clearUpdated();
	    $object -> persisted = true;
	    $found[] = $object;
	}		
	$stmt -> closeCursor();

	/** No results found, return false to signify the query was unsuccessful */
	if(!count($found)) return false; else return $found;
    }
    
    protected static function findBy($refobject = NULL, $key = '', $value = '', $limit = 0, $order_by = '', $sort_order = 'ASC')
    {
	/** Check context in which we were called */
	if(isset($refobject) && ($foreign_key = $refobject -> getForeignKey()) != NULL)
	{
	    $use_foreign_key	= true;
	    $foreign_key_value	= $refobject -> $foreign_key;
	}
	else
	{
	    $use_foreign_key = false;
	    static::setup();
	}
		
	$db	= PDOEngine::getInstance();
	$query	= "SELECT ";
	$keys 	= implode(',', array_keys(static::$column_map));
		
	$query .= $keys . ' FROM `' . static::$table_name . '` ';
		
	if(!empty($key) && !empty($value) && in_array($key, array_keys(static::$column_map)))
	    $query .= "WHERE `$key` = :value";
	    
	if($use_foreign_key)
	    $query .= " && `$foreign_key` = :fkey";
		
			
	if(!empty($order_by) && isset(static::$column_map[$order_by]))
	{
	    if($sort_order != 'ASC')
	    {
		$sort_order = 'DESC';
	    }
			
	    $query .= " ORDER BY `$order_by` $sort_order";
	}
	
	if($limit)
	{
	    if(!is_numeric($limit))
	    {
		list($start, $amount) = array_map('intval', explode(',', $limit));
		$query .= " LIMIT $start,$amount";
	    }
	    else
	    {
		$query .= "LIMIT $limit";
	    }
	}	
	
	$stmt = $db -> prepare($query);	
		
	if(!empty($key) && !empty($value))
	    $stmt -> bindParam(':value', $value);
	    
	if($use_foreign_key)
	    $stmt -> bindParam(':fkey', $foreign_key_value);
		
	$found = array();
	$stmt -> execute();
	$stmt -> setFetchMode(PDO::FETCH_CLASS, get_called_class());
		
	while($object = $stmt -> fetch())
	{
	    $object -> clearUpdated();
	    $object -> persisted = true;
	    $found[] = $object;
	}
		
	$stmt -> closeCursor();
	
	/** No results found, return false to signify the query was unsuccessful */
	if(!count($found)) return false;
		
	if(!count($found)) return false; else return $found;
    }
    
    protected static function where($refobject = NULL, $where = '', $values = array(), $limit = 0, $order_by = '', $sort_order = 'ASC')
    {
	/** Check context in which we were called */
	if(isset($refobject) && ($foreign_key = $refobject -> getForeignKey()) != NULL)
	{
	    $use_foreign_key	= true;
	    $foreign_key_value	= $refobject -> $foreign_key;
	}
	else
	{
	    $use_foreign_key = false;
	    static::setup();
	}
	
	if(!is_array($values)) $values = [$values];
		
	$db	= PDOEngine::getInstance();
	$query	= "SELECT ";
	$keys 	= implode(',', array_keys(static::$column_map));
		
	$query .= $keys . ' FROM `' . static::$table_name . '` ';
		
	$query .= "WHERE $where";
	
	if($use_foreign_key)
	{
	    $query .= " && `$foreign_key` = ?";
	    $values[] = $foreign_key_value;
	}
			
	if(!empty($order_by) && isset(static::$column_map[$order_by]))
	{
	    if($sort_order != 'ASC')
	    {
		$sort_order = 'DESC';
	    }
			
	    $query .= " ORDER BY `$order_by` $sort_order";
	}
	
	if($limit)
	{
	    if(!is_numeric($limit))
	    {
		list($start, $amount) = array_map('intval', explode(',', $limit));
		$query .= " LIMIT $start,$amount";
	    }
	    else
	    {
		$query .= "LIMIT $limit";
	    }
	}
	
	$stmt = $db -> prepare($query);
	    
	$stmt -> execute($values);
		
	$found = array();
	$stmt -> execute();
	$stmt -> setFetchMode(PDO::FETCH_CLASS, get_called_class());
		
	while($object = $stmt -> fetch())
	{
	    $object -> clearUpdated();
	    $object -> persisted = true;
	    $found[] = $object;
	}
		
	$stmt -> closeCursor();
	
	/** No results found, return false to signify the query was unsuccessful */
	if(!count($found)) return false;
		
	if(!count($found)) return false; else return $found;
    }
		
    public function save($force = false)
    {
	if(!$this -> updated) return false;
			
	if($this -> persisted)
	{
	    if(!$this -> update($force)) return false;
	}
	else
	{
	    if(!$this -> insert()) return false;
	}
		
	$this -> cache();
	return true;
    }
    
    public function delete()
    {
	$db = PDOEngine::getInstance();
	$query = 'DELETE FROM `' . static::$table_name . '` WHERE `' . static::$primary_key . '` = :pkey LIMIT 1';
	
	$stmt = $db -> prepare($query);
	
	$stmt -> bindParam(':pkey', $this -> values[static::$column_map[static::$primary_key]]);
	
	$stmt -> execute();
	$stmt -> closeCursor();
    }
    
    public function cache()
    {
	if(!$this -> cacheable) return false;
		
	$cache = CacheFactory::getInstance();
	$cache -> set('models_' . static::$table_name . '_' . $this -> values[static::$column_map[static::$primary_key]], $this, $this -> cache_expire);
    }
    
    public static function paginate($page, $per_page = 10)
    {
	return new Pagination(get_called_class(), $page, $per_page);
    }
    
    public function registerFunction($name, $closure)
    {
	if(is_callable($closure))
	{
	    $this -> functions[$name] = $closure;
	    return true;
	}
	
	return false;
    }
}
?>