<?php
namespace blog\comments;

use db;

header("Access-Control-Allow-Origin: http://blog.net21.cz");
header("Access-Control-Allow-Methods: GET,POST");
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/config/app.config.php';
require_once __DIR__ . '/config/db.config.php';
require_once __DIR__ . '/application/CommentController.php';
require_once __DIR__ . '/infrastructure/CommentRepoPDO.php';
require_once __DIR__ . '/infrastructure/db/DatabaseFactory.php';

if ($_SERVER['REMOTE_ADDR'] !== REFERRER_ADDR_ALLOWED) {
  http_response_code(403);
  die('{"error":"Unauthorized access."}');
}

$db = db\DatabaseFactory::getDatabase(DB_TYPE, DB_HOST, DB_NAME, DB_USER, DB_PASS);

$repo = new CommentRepoPDO($db->getConnection());

$controller = new CommentController($repo);

switch ($_SERVER['REQUEST_METHOD']) {  
  case 'GET':
    if (!empty($_GET['id'])) {      
      $comment = $controller->detailRequest($_GET['id']);
      if (!empty($comment)) {
        viewDetail($comment);      
      } else {
        http_response_code(404);
      }
      
    } else {
      $comments = $controller->listRequest($_GET);      
      viewCollection($comments);      
    }
    break;                           

  case 'POST':
    if (!empty($_POST['body']) && !empty($_POST['articleId'])) {
      $commentId = $controller->addRequest($_POST);
      
      http_response_code(201);
      header("Location: {$_SERVER['REQUEST_URI']}/{$commentId}");
      
    } else {
      http_response_code(400);
    }
    break;
  
  case 'OPTIONS':
    header('Allow: GET OPTIONS');
    break;
          
  default:
    http_response_code(405);
    header('Allow: GET OPTIONS');
}

function viewDetail($comment) {
  $view = array(
    'version' => '1.0',
    'href' => $_SERVER['REQUEST_URI'],
    'data' => $comment    
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