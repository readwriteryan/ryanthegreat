<?php
namespace miranda\database;

use PDO;
use PDOStatement;
use PDOException;
use miranda\logging\SystemLogger;
use miranda\config\Config;

Class PDOStmt extends PDOStatement
{   
    public function execute($args = NULL)
    {
	$start 	= microtime(true);
	$result = parent::execute($args);
	$end 	= microtime(true);	
		
	if(!$result)
	{
	    $error_information = $this -> errorInfo();
	    SystemLogger::log_event(4, 'Query execution failed with message: ' . $error_information[2]);
	}
		
	$total_time = $end - $start;
	if(Config::get('database', 'log_slow_queries') && $total_time > (float) Config::get('database', 'slow_query_time'))
	{
	    SystemLogger::log_event(5, 'Prepared statement containing: ' . $this -> queryString . ' took ' . $total_time . ' seconds to execute.');
	}
	
	return $result;
    }
}

Class PDOEngine extends PDO
{
    private static $db_instance;
    
    public function __construct($dsn = NULL, $username = NULL, $password = NULL)
    {
	if(!$dsn) $dsn			= Config::get('database', 'engine') . ':dbname=' . Config::get('database', 'database') . ';host=' . Config::get('database', 'host');
	if(!$username) $username 	= Config::get('database', 'username');
	if(!$password) $password 	= Config::get('database', 'password');
	
	/** Declared private to prevent instantiation via constructor. Use Database::getInstance() for access to the singleton database object or Database::getNewInstance for a fresh connection */
	try
	{
	    parent::__construct($dsn, $username, $password);
	}
	catch(PDOException $exception)
	{
	    echo $exception -> getMessage(); die;
	}
    }
    
    public static function getInstance()
    {
	if(!self::$db_instance)
	{	    
	    self::$db_instance = new PDOEngine();
	    self::$db_instance -> setAttribute(PDO::ATTR_STATEMENT_CLASS, array('\miranda\database\PDOStmt'));
	    self::$db_instance -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	
	return self::$db_instance;
    }
    
    public static function getNewInstance($dsn = NULL, $username = NULL, $password = NULL)
    {
	if(!$dsn) $dsn			= Config::get('database', 'engine') . ':dbname=' . Config::get('database', 'database') . ';host=' . Config::get('database', 'host');
	if(!$username) $username 	= Config::get('database', 'username');
	if(!$password) $password 	= Config::get('database', 'password');
	
	return new PDOEngine($dsn, $username, $password);
    }
    
    public function prepare($statement, $options = NULL)
    {
	if(!$options) $options = [];
	$start		= microtime(true);
	$result		= parent::prepare($statement, $options);
	$end		= microtime(true);
		
	if(Config::get('database', 'log_queries'))
	{
	    SystemLogger::log_to_file('query.log', $statement);
	}
		
	return $result;
    }
    
    public function query()
    {	
	
	$arguments	= func_get_args();
	$start		= microtime(true);
	$result		= call_user_func_array(array($this, 'parent::query'), $arguments);
	$end		= microtime(true);
		
	if(Config::get('database', 'log_queries'))
	{
	    SystemLogger::log_to_file('query.log', $arguments[0]);
	}
		
	if(!$result)
	{
	    $error_information = $this -> errorInfo();
	    SystemLogger::log_event(4, 'Query execution failed with message: ' . $error_information[2]);
	}
		
	$total_time = $end - $start;
	if(Config::get('database', 'log_slow_queries') && $total_time > (float) Config::get('database', 'slow_query_time'))
	{
	    SystemLogger::log_event(5, 'Query containing: ' . $arguments[0] . ' took ' . $total_time . ' seconds to execute.');
	}
    }
}
?>