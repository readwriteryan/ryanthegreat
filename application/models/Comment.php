<?php
namespace ryanthegreat\models;
use miranda\orm\ORM;

class Comment extends ORM
{
    protected static $table_name	= 'comments';
    protected static $primary_key	= 'id';
    protected static $column_map	= array();
    
    public function __construct()
    {
	$this -> setup();
	$this -> isCacheable(3600);
    }
    
    protected static function setup()
    {
	self::hasColumn('id');
	self::hasColumn('post_id');
	self::hasColumn('poster');
	self::hasColumn('content');
	self::hasColumn('timestamp');
	self::hasColumn('status');
    }
}
?>