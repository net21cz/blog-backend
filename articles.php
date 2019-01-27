<?php
namespace blog\articles;

use db;

header("Access-Control-Allow-Origin: http://blog.net21.cz");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/config/app.config.php';
require_once __DIR__ . '/config/db.config.php';

if ($_SERVER['HTTP_API_KEY'] !== SECRET_KEY) {
  http_response_code(403);
  die('{"error":"Unauthorized access."}');
}

require_once __DIR__ . '/application/ArticleController.php';
require_once __DIR__ . '/infrastructure/ArticleRepoPDO.php';
require_once __DIR__ . '/infrastructure/db/DatabaseFactory.php';

$db = db\DatabaseFactory::getDatabase(DB_TYPE, DB_HOST, DB_NAME, DB_USER, DB_PASS);

$repo = new ArticleRepoPDO($db->getConnection());

$controller = new ArticleController($repo);

switch ($_SERVER['REQUEST_METHOD']) {  
  case 'GET':
    $id = parseIdFromPath($_SERVER['REQUEST_URI']);
    if (!empty($id)) {
      $article = $controller->detailRequest($id);
      if (!empty($article) && !empty($article->id)) {
        viewDetail($article);
      } else {
        http_response_code(404);
      }          
      
    } else { 
      $articles = $controller->listRequest($_GET);      
      viewCollection($articles);
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

function viewCollection($articles) {
  $view = array(
    'version' => '1.0',
    'href' => $_SERVER['REQUEST_URI'],
    'articles' => array(),
    'links' => array()    
  );  
  
  $serverQuery = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';  
  $serverUri = str_replace($serverQuery, '', $_SERVER['REQUEST_URI']);
  $pagePos = strpos($serverUri, '/page');
  $baseUri = $pagePos ? substr($serverUri, 0, $pagePos) : $serverUri;
  
  foreach ($articles['data'] as $a) {
    $view['articles'][] = array(
      'href' => $baseUri . '/' . $a->id,
      'data' => $a
    );
  }
  
  $page = $articles['page'];
  $previous = $page - 1;
  $next = $page + 1;
  $first = 0;
  $last = ceil($articles['count'] / $articles['limit']) - 1;
  
  if ($previous >= 0) {
    $view['links'][] = array(
      'rel' => 'previous',
      'href' => $baseUri . ($previous > 0 ? addQueryParam($serverQuery, 'page', $previous) : removeQueryParam($serverQuery, 'page'))
    );    
  }
  if ($next <= $last) {
    $view['links'][] = array(
      'rel' => 'next',
      'href' => $baseUri . addQueryParam($serverQuery, 'page', $next)
    );
  }
  $view['links'][] = array(
    'rel' => 'first',
    'href' => $baseUri . removeQueryParam($serverQuery, 'page')
  );
  $view['links'][] = array(
    'rel' => 'last',
    'href' => $baseUri . ($last > 0 ? addQueryParam($serverQuery, 'page', $last) : removeQueryParam($serverQuery, 'page'))
  );
  
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

function removeQueryParam($queryString, $paramName) {
  $queryString = preg_replace('/' . $paramName . '=(\w+)/i', '', $queryString);
  
  $len = strlen($queryString);
  if (strpos($queryString, '?') === $len - 1) {
    $queryString = substr($queryString, 0, $len - 2);
  }
  if (strpos($queryString, '&') === $len - 1) {
    $queryString = substr($queryString, 0, $len - 1);
  }
  return str_replace('?&', '?', $queryString);
}

function parseIdFromPath($path) {
  $path = !empty($path) && $path[strlen($path) - 1] == '/' ? substr($path, 0, strlen($path) - 1) : $path;
  if (empty($path)) {
    return array();
  }
  $queryPos = strpos($path, '?');
  if ($queryPos !== FALSE) {
    $path = substr($path, 0, $queryPos);
  }
  $parts = explode('/', $path[0] == '/' ? substr($path, 1) : $path);
  $lastPart = $parts[sizeof($parts) - 1];
  return $lastPart != 'articles' ? $lastPart : null;
}
