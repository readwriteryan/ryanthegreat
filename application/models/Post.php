<?php
namespace ryanthegreat\models;
use miranda\orm\ORM;

class Post extends ORM
{
    protected static $table_name	= 'posts';
    protected static $primary_key	= 'id';
    protected static $column_map	= array();
    
    public function __construct()
    {
	$this -> setup();
	$this -> isCacheable(3600);
	$this -> hasRelationship('comments', '\ryanthegreat\models\Comment', 'post_id');
    }
    
    protected static function setup()
    {
	self::hasColumn('id');
	self::hasColumn('title');
	self::hasColumn('description');
	self::hasColumn('content');
	self::hasColumn('timestamp');
    }
}
?>