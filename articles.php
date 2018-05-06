<?php
namespace articles;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/config/db.config.php';
require_once __DIR__ . '/application/ArticleController.php';
require_once __DIR__ . '/infrastructure/ArticleRepoPDO.php';
require_once __DIR__ . '/infrastructure/db/DatabaseFactory.php';

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
  
  case 'OPTIONS':
    header('Allow: GET OPTIONS');
    break;
          
  default:
    http_response_code(405);
    header('Allow: GET OPTIONS');
}    

echo json_encode($response);