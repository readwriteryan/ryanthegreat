<?php
namespace miranda\views;

use miranda\config\Config;
use miranda\plugins\SecureHash;
use miranda\plugins\ORM;

class View
{
    protected $visible	= [];
    public $css		= [];
    public $js		= [];
    public $session	= NULL;
    
    private static function escape($data)
    {
	if(is_string($data))
	    return htmlentities($data, ENT_QUOTES, Config::get('site', 'charset'));
	else if(is_object($data) && is_callable([$data, 'escape']))
	    return $data -> escape();
	else if(is_object($data))
	{
	    foreach(get_object_vars($data) as $key => $value) $data -> $key = htmlentities($data -> $key, ENT_QUOTES, Config::get('site', 'charset'));
	    return $data;
	}
	else if(is_array($data))
	    return array_map('self::escape', $data);
	else
	    return $data;
    }
    
    public function css($stylesheet)
    {
	$this -> css[] = format(Config::get('locations', 'css') . self::escape($stylesheet) . '.css');
	
	return $this;
    }
    
    public function js($source)
    {
	$this -> js[] = format(Config::get('locations', 'js') . self::escape($source) . '.js');
	
	return $this;
    }
    
    public function visible($set, $value = '')
    {
	if(is_array($set))
	{
	    foreach($set as $key => $value)
	    {
		$this -> visible[$key] = self::escape($value);
	    }
	}
	else
	{
	    $this -> visible[$set] = self::escape($value);
	}
	
	return $this;
    }
    
    public function raw($set, $value = '')
    {
	if(is_array($set))
	{
	    foreach($set as $key => $value)
	    {
		$this -> visible[$key] = $value;
	    }
	}
	else
	{
	    $this -> visible[$set] = $value;
	}
	
	return $this;
    }
    
    public function clearVisible()
    {
	$this -> visible = array();
	
	return $this;
    }
    
    public function render($view)
    {
	extract($this -> visible);
		
	if($template = Config::get('views', 'template')) require_once(Config::get('site', 'webroot') . Config::get('locations', 'global') . $template);
		
	require_once(Config::get('site', 'webroot') . Config::get('locations', 'views') . $view . '.html.php');
		
	if($footer = Config::get('views', 'footer')) require_once(Config::get('site', 'webroot') . Config::get('locations', 'global') . $footer); 
    }
    
    public function render_partial($partial, $visible = NULL)
    {
	if(is_array($visible)) extract($visible);
	
	require(Config::get('site', 'webroot') . Config::get('locations', 'partials') . $partial .'.html.php');
    }
    
    public function form_token()
    {
	$token = SecureHash::hash('sha256', uniqid(mt_rand(10000, 99999), true), Config::get('namespace', 'base'), false);
	
	if(isset($this -> session))
	    $this -> session -> miranda_form_token = $token;
	else
	    setcookie('miranda_form_token', $token, 0, Config::get('cookies', 'path'), Config::get('cookies', 'domain'), false, true);
	    
	return $token;
    }
}