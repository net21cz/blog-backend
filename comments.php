<?php
namespace blog\comments;

use db;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/config/db.config.php';
require_once __DIR__ . '/application/CommentController.php';
require_once __DIR__ . '/infrastructure/CommentRepoPDO.php';
require_once __DIR__ . '/infrastructure/db/DatabaseFactory.php';

$db = db\DatabaseFactory::getDatabase(DB_TYPE, DB_HOST, DB_NAME, DB_USER, DB_PASS);

$repo = new CommentRepoPDO($db->getConnection());

$controller = new CommentController($repo);

switch ($_SERVER['REQUEST_METHOD']) {  
  case 'GET':
    if (isset($_GET['id'])) {      
      $comment = $controller->detailRequest($_GET['id']);
      if (!empty($comments)) {
        viewDetail($comment);      
      } else {
        http_response_code(404);
      }
      
    } else {
      $comments = $controller->listRequest($_GET);
      
      if (!empty($comments)) {
        viewCollection($articles);
      } else {
        http_response_code(404);
      }
    }
    break;                           
  
  case 'OPTIONS':
    header('Allow: GET OPTIONS');
    break;
          
  default:
    http_response_code(405);
    header('Allow: GET OPTIONS');
}

function viewDetail($article) {
  $view = array(
    'version' => '1.0',
    'href' => $_SERVER['REQUEST_URI'],
    'data' => $article    
  );
  echo json_encode($view);
}

function viewCollection($comments) {
  $view = array(
    'version' => '1.0',
    'href' => $_SERVER['REQUEST_URI'],
    'comments' => $comments 
  );
  echo json_encode($view);
}