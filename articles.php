<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once 'config/db.config.php';
include_once 'infrastructure/db/DatabaseFactory.php';
include_once 'infrastructure/ArticleRepoPDO.php';
include_once 'application/ArticleController.php';

$db = DatabaseFactory::getDatabase(DB_TYPE, DB_HOST, DB_NAME, DB_USER, DB_PASS);

$repo = new ArticleRepoPDO($db->getConnection());

$controller = new ArticleController($repo);

$response = null;

switch ($_SERVER['REQUEST_METHOD']) {  
  case 'GET':
    if (isset($_GET['id'])) {
      $response = $controller->detailRequest($_GET['id']);
      
    } else {
      $response = $controller->listRequest($_GET);
    }
    break;                           
        
  default:
    throw new Exception('HTTP method not supported: ' . $_SERVER['REQUEST_METHOD']);
}    

echo json_encode($response);