<?php
namespace ryanthegreat\controllers;
require_once(dirname(__FILE__) . '/../../miranda/miranda.php');

use miranda\cache\ResultCache;
use ryanthegreat\controllers\Base;
use ryanthegreat\models\Post;

class Posts extends Base
{
    public function __construct()
    {
	$this -> before_except('check_admin_ip', ['index', 'show']);
	$this -> before('check_admin_password', ['create', 'update']);
	$this -> before('validate_form_token', ['create', 'update']);
    }
    
    public function show()
    {
	$post_id = isset($this -> params['id']) ? (int) $this -> params['id'] : 0;
	
	if(!$post_id || !($post = Post::findOne($post_id)))
	    throw new GeneralException('Post not found');
	    
	$comments = $post -> comments -> where('status = ?', 1);
	    
	$this -> view -> visible(['title' => $post -> title, 'comments' => $comments]) -> raw(['post' => $post]);
	$this -> view -> js('submitcomment') ->js('highlight.pack') -> js('hljs-init') -> css('github-style');
	$this -> view -> render('posts/view');
    }
    
    public function add()
    {	    
	$this -> view -> visible(['title' => 'Create A New Post']);
	$this -> view -> render('posts/new');
    }
    
    public function create()
    {
	$post = new Post;
	$post -> title 		= $_POST['title'];
	$post -> description	= $_POST['description'];
	$post -> content	= $_POST['content'];
	$post -> timestamp	= time();
	$post -> save();
	
	$this -> view -> visible(['title' => 'Admin Backend', 'posted' => true]) -> render('posts/new');
    }
    
    public function update()
    {
	$post_id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
	
	if(!$post_id || !($post = Post::findOne($post_id)))
	    throw new GeneralException('Post not found');
	
	$post -> title 		= $_POST['title'];
	$post -> description	= $_POST['description'];
	$post -> content	= $_POST['content'];
	$post -> save();
	
	$this -> view -> visible(['title' => 'Edit: ' . $post -> title, 'updated' => true, 'post' => $post]) -> render('posts/edit');
    }
    
    public function edit()
    {
	$post_id = isset($this -> params['id']) ? (int) $this -> params['id'] : 0;
	
	if(!$post_id || !($post = Post::findOne($post_id)))
	    throw new GeneralException('Post not found');
	    
	$this -> view -> visible(['title' => 'Edit: ' . $post -> title]) -> raw(['post' => $post]) -> render('posts/edit');
    }
    
    public function index()
    {
	$page = isset($this -> params['page']) ? (int) $this -> params['page'] : 1;
	
	$posts = ResultCache::get('posts_' . $page, function() use ($page) {return Post::paginate($page) -> order_by('timestamp', 'desc') -> fetch();});
	
	$this -> view -> visible(['title' => 'Posts By Ryan Wagner', 'posts' => $posts]) -> render('posts/page');
    }
}
?>