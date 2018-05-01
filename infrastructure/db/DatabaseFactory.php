<?php
include_once 'DatabaseMySql.php';

class DatabaseFactory {
  
  public static function getDatabase($type, $host, $db, $username, $password) {
    switch ($type) { 
        
      case 'mysql':
        return new DatabaseMySql($host, $db, $username, $password);
        
      default:
        throw new Exception('Unknown database type: ' . $type);
    } 
  }
}
