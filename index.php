<?php
namespace blog;

use db;

header("Access-Control-Allow-Origin: " . ORIGIN_URL);
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/config/app.config.php';
require_once __DIR__ . '/config/db.config.php';

if ($_SERVER['HTTP_API_KEY'] !== SECRET_KEY) {
  http_response_code(403);
  die('{"error":"Unauthorized access."}');
} 

require_once __DIR__ . '/application/IndexController.php';
require_once __DIR__ . '/infrastructure/OptionsRepoPDO.php';
require_once __DIR__ . '/infrastructure/CategoryRepoPDO.php';
require_once __DIR__ . '/infrastructure/AuthorRepoPDO.php';
require_once __DIR__ . '/infrastructure/db/DatabaseFactory.php';

$db = db\DatabaseFactory::getDatabase(DB_TYPE, DB_HOST, DB_NAME, DB_USER, DB_PASS);

$optionsRepo = new OptionsRepoPDO($db->getConnection());
$categoryRepo = new CategoryRepoPDO($db->getConnection());
$authorRepo = new AuthorRepoPDO($db->getConnection());

$controller = new IndexController($categoryRepo, $authorRepo, $optionsRepo);

switch ($_SERVER['REQUEST_METHOD']) {  
  case 'GET':
    $bloginfo = $controller->blogInfoRequest();    
    view($bloginfo);
    break;                           
  
  case 'OPTIONS':
    header('Allow: GET OPTIONS');
    break;
          
  default:
    http_response_code(405);
    header('Allow: GET OPTIONS'); 
} 

function view($bloginfo) {
  $view = array(
    'version' => '1.0',
    'href' => $_SERVER['REQUEST_URI'],
    'title' => $bloginfo->title,
    'description' => $bloginfo->description,
    'categories' => array(),
    'authors' => array(),
    'links' => array()
  );
  
  foreach ($bloginfo->categories as $c) {
    $view['categories'][] = array(
      'id' => $c->id,
      'name' => $c->name
    );
  }
  
  foreach ($bloginfo->authors as $a) {
    $view['authors'][] = array(
      'id' => $a->id,
      'name' => $a->name,
      'email' => $a->email
    );
  }
  
  $view['links'][] = array(
    'rel' => 'articles',
    'href' => $_SERVER['REQUEST_URI'] . 'articles'
  ); 
  
  echo json_encode($view);
}   
