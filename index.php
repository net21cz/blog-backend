<?php
namespace blog;

use db;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/config/db.config.php';
require_once __DIR__ . '/application/IndexController.php';
require_once __DIR__ . '/infrastructure/CategoryRepoPDO.php';
require_once __DIR__ . '/infrastructure/OptionsRepoPDO.php';
require_once __DIR__ . '/infrastructure/db/DatabaseFactory.php';

$db = db\DatabaseFactory::getDatabase(DB_TYPE, DB_HOST, DB_NAME, DB_USER, DB_PASS);

$categoryRepo = new CategoryRepoPDO($db->getConnection());
$optionsRepo = new OptionsRepoPDO($db->getConnection());

$controller = new IndexController($categoryRepo, $optionsRepo);

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
    'title' => $bloginfo->title(),
    'description' => $bloginfo->description(),
    'categories' => array(),
    'links' => array()
  );
  
  foreach ($bloginfo->categories() as $c) {
    $view['categories'][] = array(
      'title' => $c->name(),
      'href' => $_SERVER['REQUEST_URI'] . '/articles?categoryId=' . $c->id()
    );
  }
  
  $view['links'][] = array(
    'rel' => 'articles',
    'href' => '/api/articles'
  ); 
  
  echo json_encode($view);
}   

