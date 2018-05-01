<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once 'config/db.config.php';
include_once 'infrastructure/db/DatabaseFactory.php';
include_once 'infrastructure/ArticleRepoImpl.php';

$db = DatabaseFactory::getDatabase(DB_TYPE, DB_HOST, DB_NAME, DB_USER, DB_PASS);

$repo = new ArticleRepoImpl($db->getConnection());

$response = null;

switch ($_SERVER['REQUEST_METHOD']) {  
  case 'GET':
    if (isset($_GET['id'])) {
      $response = detailRequest($_GET['id'], $repo);
      
    } else {
      $response = listRequest($_GET, $repo);
    }
    break;                           
        
  default:
    throw new Exception('HTTP method not supported: ' . $_SERVER['REQUEST_METHOD']);
}    

echo json_encode($response);

// ///////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Get Article Detail.
 */ 
function detailRequest($id, $repo) {
  $article = $repo->fetchOne($id);
  
  return $article;
}

/**
 * List Articles.
 */ 
function listRequest($params, $repo) {
  $limit = 10;
  
  $categoryId = getIfSet($params, 'categoryId');   
  $authorId = getIfSet($params, 'authorId');   
  $page = getIfSet($params, 'page', 0);
  
  $articles = $repo->fetchAll($categoryId, $authorId, $page * $limit, $limit);
  
  return $articles;
}

// ///////////////////////////////////////////////////////////////////////////////////////////////////

function getIfSet($params, $var, $def = null) {
  return isset($params[$var]) ? $params[$var] : $def;
} 