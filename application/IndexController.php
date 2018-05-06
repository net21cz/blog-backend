<?php
namespace articles;

require_once __DIR__ . '/../domain/CategoryRepo.php';
require_once __DIR__ . '/../domain/OptionsRepo.php';

class ArticleController {

  private $repo;

  public function __construct(CategoryRepo $categoryRepo, OptionsRepo $optionsRepo){                                           
    $this->repo = $repo;
  }
  
  public function indexRequest() {
    $limit = 10;
    
    $categoryId = $this->getIfSet($params, 'categoryId');   
    $authorId = $this->getIfSet($params, 'authorId');   
    $page = $this->getIfSet($params, 'page', 0);
    
    $articles = $this->repo->fetchAll((int)$categoryId, (int)$authorId, $page * $limit, $limit);
    
    return $articles;
  }
  
  private function getIfSet($params, $var, $def = null) {
    return isset($params[$var]) ? $params[$var] : $def;
  }
}