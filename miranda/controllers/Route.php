<?php
namespace miranda\controllers;
use miranda\cache\CacheFactory;
use miranda\config\Config;

Class Request
{
    protected static $instance = NULL;
    protected $path;
    protected $method;
    protected static $mapping = array(
				    'get'	=> 1,
				    'post' 	=> 2,
				    'put'	=> 3,
				    'delete' 	=> 4,
				    'default'	=> 1);
    
    protected function __construct()
    {
	$this -> getPath();
	$this -> getMethod();
    }
    
    public static function getInstance()
    {
	if(self::$instance == NULL) self::$instance = new Request();
	
	return self::$instance;
    }
    
    public function __get($member)
    {
	return $this -> $member;
    }
    
    public static function mapping($key)
    {
	return isset(self::$mapping[strtolower($key)]) ? self::$mapping[strtolower($key)] : false;
    }
    
    private function getMethod()
    {
	$method = strtolower($_SERVER['REQUEST_METHOD']);
	
	if(isset($_POST['http_method_override']))
	    $method = strtolower($_POST['http_method_override']);
		
	if(isset(self::$mapping[$method]))	$this -> method = self::$mapping[$method];
	else					$this -> method = self::$mapping['default'];
		
    }
    
    private function getPath()
    {
	if(isset($_SERVER['PATH_INFO']))		$this -> path = $_SERVER['PATH_INFO'];
	else if(isset($_SERVER['ORIG_PATH_INFO']))	$this -> path = $_SERVER['ORIG_PATH_INFO'];
	else						$this -> path = false;
    }
}


Class Route
{
    private static $routes		= array();
    private static $default_route	= NULL;
    private static $root		= NULL;
    
    public static function get($route, $action, $restrictions = array())
    {
	self::$routes[Request::mapping('get')][self::processRoute($route, $restrictions)] = $action; 
    }
    
    public static function post($route, $action, $restrictions = array())
    {
	self::$routes[Request::mapping('post')][self::processRoute($route, $restrictions)] = $action; 
    }
    
    public static function put($route, $action, $restrictions = array())
    {
	self::$routes[Request::mapping('put')][self::processRoute($route, $restrictions)] = $action; 
    }
    
    public static function delete($route, $action, $restrictions = array())
    {
	self::$routes[Request::mapping('delete')][self::processRoute($route, $restrictions)] = $action; 
    }
    
    public static function custom($route, $action, $method)
    {
	$method 	= strtolower($method);
	$request 	= Request::getInstance();
		
	if(!($method = Request::mapping($method))) $method = Request::mapping('default');
		
	self::$routes[$method][self::processRoute($route)] = $action;
    }
    
    public static function other($action)
    {
	self::$default_route = $action;
    }
    
    public static function root($action)
    {
	self::$root					= $action;
	self::$routes[Request::mapping('get')]['/']	= $action;
    }
    
    public static function resource($model)
    {
	$controller = $model;
	
	self::get("/$model/:id", "$controller#show", ['id' => 'int']);
	self::get("/$model/:id/edit", "$controller#edit", ['id' => 'int']);
	self::get("/$model/new", "$controller#add");
	self::post("/$model", "$controller#create");
	self::put("/$model/:id", "$controller#update", ['id' => 'int']);
	self::delete("/$model/:id", "$controller#delete", ['id' => 'int']);
    }
    
    public static function resources($model)
    {
	$controller = $model;
	
	self::resource($model);
	self::get("/$model", "$controller#index");
    }
    
    public static function start()
    {
	$request	= Request::getInstance();
	$params		= [];
	
		
	if($request -> path != false)
	{
	    /** Find route matching supplied path */
	    if(isset(self::$routes[$request -> method]))
	    {
		$cache = CacheFactory::getInstance();
		/** Load route actions from cache if possible */
		if($result = $cache -> get('routes_' . md5($request -> path)))
		{
		    self::action($result[0], array_slice($result, 1), $request);
		    exit;
		}
		else
		{
		    foreach(self::$routes[$request -> method] as $route => $action)
		    {   
			if(preg_match('`^' . $route . '$`', $request -> path, $params))
			{
			    self::action($action, array_slice($params, 1), $request, true);
			    exit;
			}
		    }
		}
	    }
	    
	    /** Trigger default route */
	    self::action(self::$default_route, $params, $request);
	}
	else
	{
	    /** Trigger root route */
	    self::action(self::$root, $params, $request);
	}
    }
    
    protected static function action($action, $params, $request, $cache = false)
    {
	list($controller, $function)	= explode('#', $action);
	$controller 			= Config::get('namespace', 'controllers') . ucfirst($controller);
	$controller			= $controller::getInstance();
	
	$controller -> controller_setup($params);
    
	if($cache)
	{
	    /** Cache the route, action and params for faster processing */
	    $cache = CacheFactory::getInstance();
	    
	    array_unshift($params, $action);
	    $cache -> set('routes_' . md5($request -> path), $params, 0);
	}
	
	$controller -> execute_before($function);	    	
	$controller -> $function();
	$controller -> execute_after($function);
    }
    
    protected static function processRoute($route, $restrict)
    {
	$original_route	= $route;
	$cache		= CacheFactory::getInstance();
		
	/** Read route information from cache if possible */
	if($cacheRoute = $cache -> get('route_rewrite_' . md5($route))) return $cacheRoute;
		   
	$restrictions = array(	'int' 		=> '(?<$1>[0-9]+)',
				'float' 	=> '(?<$1>[0-9\.]+)',
				'alpha' 	=> '(?<$1>[a-zA-Z]+)',
				'alphanum'	=> '(?<$1>[a-zA-Z0-9]+)');
			  
	$route = str_replace(')', ')?', str_replace('(', '(?:',$route));
		
	foreach($restrict as $variable => $restriction)
	{
	    $route = preg_replace("`:($variable)`", $restrictions[$restriction], $route);
	}
		
	$route = preg_replace('`:([a-zA-Z][a-zA-Z0-9]*)`', '(?<$1>[^/]+)', $route);
	$cache -> set('route_rewrite_' . md5($original_route), $route, 0);
		
	return $route;
    }
}
?>
