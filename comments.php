<?php
namespace blog\comments;

use db;

header("Access-Control-Allow-Origin: http://blog.net21.cz");
header("Access-Control-Allow-Methods: GET,POST");
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/config/app.config.php';
require_once __DIR__ . '/config/db.config.php';

if ($_SERVER['HTTP_API_KEY'] !== SECRET_KEY) {
  http_response_code(403);
  die('{"error":"Unauthorized access."}');
}

require_once __DIR__ . '/application/CommentController.php';
require_once __DIR__ . '/infrastructure/CommentRepoPDO.php';
require_once __DIR__ . '/infrastructure/db/DatabaseFactory.php';

$db = db\DatabaseFactory::getDatabase(DB_TYPE, DB_HOST, DB_NAME, DB_USER, DB_PASS);

$repo = new CommentRepoPDO($db->getConnection());

$controller = new CommentController($repo);

switch ($_SERVER['REQUEST_METHOD']) {  
  case 'GET':
    $articleId = parseArticleIdFromPath($_SERVER['REQUEST_URI']);
    if (!empty($articleId)) {
      $commentId = parseCommentIdFromPath($_SERVER['REQUEST_URI']);
      if (!empty($commentId)) {
        $result = $controller->answersRequest($commentId, (int)$_GET['page']);
        viewAnswers($result);
      } else {            
        $result = $controller->listRequest($articleId, (int)$_GET['page']);
        viewComments($result);
      }            
    } else {
      http_response_code(400);
    }
    break;                           

  case 'POST':
    if (!empty($_POST['author']) && !empty($_POST['body']) && !empty($_POST['articleId'])) {
      $controller->addRequest($_POST);      
      http_response_code(201);
      
    } else {
      http_response_code(400);
    }
    break;
  
  case 'OPTIONS':
    header('Allow: GET POST OPTIONS');
    break;
          
  default:
    http_response_code(405);
    header('Allow: GET POST OPTIONS');
}

function viewComments($result, $next = null) {  
  $serverUri = str_replace(!empty($_SERVER['QUERY_STRING']) ? "?{$_SERVER['QUERY_STRING']}" : '', '', $_SERVER['REQUEST_URI']);
  
  $comments = array();  
  foreach ($result['comments'] as $comment) {
     $comment->next = !empty($comment->next) ? "{$serverUri}/{$comment->id}?page={$comment->next}" : null;
     $comments[] = $comment;
  }
    
  $view = array(
    'version' => '1.0',
    'href' => $_SERVER['REQUEST_URI'],
    'comments' => $comments 
  );
  if (!empty($result['next'])) {
    $view['next'] = $serverUri . '?page=' . $result['next'];
  }
  echo json_encode($view);
}

function viewAnswers($result, $next = null) {  
  $serverUri = str_replace(!empty($_SERVER['QUERY_STRING']) ? "?{$_SERVER['QUERY_STRING']}" : '', '', $_SERVER['REQUEST_URI']);
      
  $view = array(
    'version' => '1.0',
    'href' => $_SERVER['REQUEST_URI'],
    'comments' => $result['answers'] 
  );
  if (!empty($result['next'])) {
    $view['next'] = $serverUri . '?page=' . $result['next'];
  }
  echo json_encode($view);
}

function addQueryParam($queryString, $paramName, $paramValue) {
  $replacement = $paramName . '=' . urlencode($paramValue);
  if (empty($queryString)) {
    return '?' . $replacement;
  }
  $queryString = preg_replace('/' . $paramName . '=(\w+)/i', $replacement, $queryString);
  if (!strpos($queryString, $replacement)) {
    $queryString .= '&' . $replacement;
  }
  return $queryString;
}

function parseArticleIdFromPath($path) {
  $path = !empty($path) && $path[strlen($path) - 1] == '/' ? substr($path, 0, strlen($path) - 1) : $path;
  if (empty($path)) {
    return array();
  }
  $queryPos = strpos($path, '?');
  if ($queryPos !== FALSE) {
    $path = substr($path, 0, $queryPos);
  }
  $parts = explode('/', $path[0] == '/' ? substr($path, 1) : $path);
  $firstParamIndex = array_search('comments', $parts) + 1;
  return $firstParamIndex < sizeof($parts) ? $parts[$firstParamIndex] : null;
}

function parseCommentIdFromPath($path) {
  $path = !empty($path) && $path[strlen($path) - 1] == '/' ? substr($path, 0, strlen($path) - 1) : $path;
  if (empty($path)) {
    return array();
  }
  $queryPos = strpos($path, '?');
  if ($queryPos !== FALSE) {
    $path = substr($path, 0, $queryPos);
  }
  $parts = explode('/', $path[0] == '/' ? substr($path, 1) : $path);  
  $firstParamIndex = array_search('comments', $parts) + 1;
  return $firstParamIndex + 1 < sizeof($parts) ? $parts[$firstParamIndex + 1] : null;
}