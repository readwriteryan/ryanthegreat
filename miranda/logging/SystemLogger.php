<?php
namespace miranda\logging;

use PDO;
use miranda\database\pdoengine;
use miranda\exceptions\SystemException;

/** Logging Definitions */
define('LOG_GENERAL_EXCEPTIONS', 1);						/** 1 to log general exceptions, 0 to turn logging off */
define('LOG_SECURITY_EXCEPTIONS', 1);						/** 1 to log security exceptions, 0 to turn logging off */
define('LOG_SYSTEM_EXCEPTIONS', 1); 						/** 1 to log system exceptions, 0 to turn logging off */
define('LOG_TO_DATABASE', 1);							/** 1 to turn logging to a database table on, 0 to disable */
define('LOG_TABLE_NAME', 'system_logs');					/** If database logging is enabled, system logs will be saved to this table */
define('LOG_TO_FILE', 1);							/** 1 to turn logging to the filesystem on, 0 to disable */
define('LOG_FILE', 'system.log');						/** If filesystem logging is enabled, system logs will be saved to this file name */
define('LOG_FILE_LOCATION', '/srv/http/ryanthegreat/application/logs/'); 	/** If filesystem logging is enabled, system logs will be saved in this location */

Class SystemLogger
{
    private static function getFileHandle($strLocation)
    {
	$attempt = 0;
	while(!($fh = @fopen($strLocation, 'a')) && $attempt < 5)
	{
	    usleep(100);
	    $fh = @fopen($strLocation, 'a');
	    ++$attempt;
	}
			
	if(!$fh)
	{
	    throw new SystemException('Failed to successfully open log file:' . $strLocation);
	}
			
	return $fh;
    }
    
    private static function getFileLock($fileHandle, $strFileLocation)
    {
	$attempt = 0;
	while(!($locked = @flock($fileHandle, LOCK_EX)) && $attempt < 5)
	{
	    usleep(100);
	    $locked = @flock($fileHandle, LOCK_EX);
	    ++$attempt;
	}
		
	if(!$locked)
	{
	    throw new SystemException('Could not get lock for file: ' . $strFileLocation);
	}
		
	return $locked;
    }
    
    private static function removeFileLock($fileHandle, $strFileLocation)
    {
	$attempt = 0;
	while(!($unlocked = @flock($fileHandle, LOCK_UN)) && $attempt < 5)
	{
	    usleep(100);
	    $unlocked = @flock($fileHandle, LOCK_UN);
	    ++$attempt;
	}
		
	if(!$unlocked)
	{
	    throw new SystemException('Could not remove lock for file: ' . $strFileLocation);
	}
		
	return $unlocked;
    }
    
    private static function write_log($logfileHandle, $log_data, $strFileLocation)
    {	
	if(!($written = @fwrite($logfileHandle, $log_data, strlen($log_data))))
	{
	    $attempt = 0;
	    while(!$written && $attempt < 3)
	    {
		usleep(100);
		$written = @fwrite($logfileHandle, $log_data, strlen($log_data));
		++$attempt;
	    }
			
	    if(!$written)
	    {
		self::removeFileLock($logfileHandle, $strFileLocation);
		fclose($logfileHandle);
		throw new SystemException('Could not write to file: ' . $strFileLocation);
	    }
	}
		
	self::removeFileLock($logfileHandle, $strFileLocation);
	fclose($logfileHandle);
	return $written;
    }
    
    public static function log_event($intEventType, $strEvent)
    {
	if(LOG_TO_DATABASE)
	{
	    $db		= PDOEngine::getInstance();
	    $log_table	= LOG_TABLE_NAME;
	    $stmt 	= $db -> prepare("INSERT INTO `$log_table` (`id`, `type`, `event`, `timestamp`) VALUES(0, :type, :event, UNIX_TIMESTAMP())");
			
	    $stmt -> bindParam(':type', $intEventType, PDO::PARAM_INT);
	    $stmt -> bindParam(':event', $strEvent, PDO::PARAM_STR);
	    $stmt -> execute();
	    $stmt -> closeCursor();
	}
	if(LOG_TO_FILE)
	{
	    $logfile = self::getFileHandle(LOG_FILE_LOCATION . LOG_FILE);
			
	    if(self::getFileLock($logfile, LOG_FILE_LOCATION . LOG_FILE))
	    {
		$timestamp	= time();
		$log_data	= "$intEventType\t$strEvent\t$timestamp" . PHP_EOL;
		
		self::write_log($logfile, $log_data, LOG_FILE_LOCATION . LOG_FILE);
	    }
	}
    }
    
    public static function log_to_file($filename, $data)
    {
	$location	= LOG_FILE_LOCATION . $filename;
	$fh		= self::getFileHandle($location);
	$data 		= $data . PHP_EOL;	
		
	if(self::getFileLock($fh, $location))
	{
	    self::write_log($fh, $data, $location);
	}
    }
}
?>