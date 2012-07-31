<?php
namespace ryanthegreat\controllers;
require_once(dirname(__FILE__) . '/../../miranda/miranda.php');

use miranda\cache\ResultCache;
use miranda\config\Config;
use ryanthegreat\controllers\Base;
use ryanthegreat\models\Post;
use ryanthegreat\models\Comment;

class Comments extends Base
{
	public function __construct()
	{
		$this -> before('check_admin_ip', ['index', 'approve', 'decline']);
	}
	
	public function create()
	{		
		$error		= false;
		$status		= false;
		$post_id	= isset($this -> params['id']) ? (int) $this -> params['id'] : 0;
		$post 		= Post::findOne($post_id);
		$comment	= $post -> comments -> getNew();				
		
		if(!$post) 			$error = 'Could not post comment because the associated post could not be found.';
		if(!$error && !$comment) 	$error = 'Could not create a new comment for this post.';
		
		
		if(!$error)
		{
			$comment -> poster	= $_POST['poster'];
			$comment -> content	= $_POST['comment'];
			$comment -> timestamp	= time();
			$comment -> status	= 0;
			
			$status = $comment -> save();
			
			if(!$status) $error = 'Unable to save comment at this time, please try again later.';
		}
		
		echo json_encode(['status' => $status, 'error' => $error]);
	}
	
	public function index()
	{
		$comments = Comment::where('status = ?', 0);		
		$this -> view -> visible(['title' => 'Pending Comments', 'comments' => $comments]) -> render('comments/index');
	}
	
	public function approve()
	{
		$comment = Comment::findOne(isset($this -> params['id']) ? (int) $this -> params['id'] : 0);
		
		if($comment)
		{
			$comment -> status = 1;
			$comment -> save ();
		}
		
		$this -> redirect('comments');
	}
	
	public function decline()
	{
		$comment = Comment::findOne(isset($this -> params['id']) ? (int) $this -> params['id'] : 0);
		
		if($comment) $comment -> delete();
		
		$this -> redirect('comments');
	}
}
?>