<?php
namespace miranda\autoloader;

Class Autoloader
{
    static protected $namespaces = array();
    static protected $registered = false;
    
    protected function __construct() {}
    
    protected static function register()
    {
	self::$registered = spl_autoload_register('\miranda\autoloader\Autoloader::autoload');
	return self::$registered;
    }
    
    public static function registerNamespace($namespace, $location)
    {
	self::$namespaces[$namespace] = $location;
	return self::$registered ? true : self::register();
    }
    
    public static function autoload($loadNamespace)
    {
	$load		= ltrim($loadNamespace, '\\');
	$namespace	= substr($load, 0, strpos($load, '\\'));
		
	if(isset(self::$namespaces[$namespace]))
	{
	    $class_name		= str_replace('\\', '/', substr($load, strpos($load, '\\') + 1)) . '.php';
	    $file_location	= self::$namespaces[$namespace] . '/' . $class_name;
	    
	    if(!file_exists($file_location)) die(debug_print_backtrace());
	    return require_once($file_location);
	}
    }
}
?>