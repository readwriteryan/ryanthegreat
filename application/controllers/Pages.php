<?php
namespace ryanthegreat\controllers;
require_once(dirname(__FILE__) . '/../../miranda/miranda.php');

use miranda\views\View;
use ryanthegreat\controllers\Base;
use ryanthegreat\models\Post;

class Pages extends Base
{
    public function home()
    {
	Post::update();
	$this -> view -> visible(['title' => 'Home Page', 'session' => $this -> session]) -> render('pages/home');
    }
    
    public function about()
    {
	$this -> view -> visible(['title' => 'About Ryan Wagner']);
	$this -> view -> render('pages/about');
    }
    
    public function error404()
    {
	$this -> view -> visible(['title' => 'Error 404']);
	$this -> view -> render('pages/error404');
    }
    
    public static function handleException($exception)
    {
	$view = new View;
	$view -> visible(array('exception' => $exception));
	$view -> render('pages/exception');
    }
    
    public function index()
    {
	$this -> view -> visible(['title' => 'Home Page']) -> render('pages/home');
    }
    
}
?>