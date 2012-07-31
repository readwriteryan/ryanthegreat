<?php
namespace ryanthegreat\controllers;
require_once(dirname(__FILE__) . '/../../miranda/miranda.php');

use miranda\controllers\Controller;
use miranda\config\Config;
use miranda\plugins\SecureHash;

/** This class extends from miranda's controller to provide functionality which all controllers on ryanthegreat should have */
class Base extends Controller
{
    public function check_admin_ip()
    {
	if(!in_array($_SERVER['REMOTE_ADDR'], explode(',', Config::get('admin', 'ip'))))
	   $this -> redirect('home');
    }

    public function check_admin_password()
    {
	$unique		= '';
	$password	= $_POST['admin_password'];
	
	if(Config::get('admin', 'hash') !== SecureHash::hash(Config::get('admin', 'hash_type'), $password, $unique, false))
	    $this -> redirect('home');
    }
}
?>