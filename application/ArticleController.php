<?php
namespace blog\articles;

require_once __DIR__ . '/../domain/ArticleRepo.php';

class ArticleController {

  private $repo;

  public function __construct(ArticleRepo $repo){                                           
    $this->repo = $repo;
  }

  public function detailRequest($id) {
    $article = $this->repo->fetchOne((int)$id);
    
    return $article;
  }
  
  public function listRequest($params) {
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