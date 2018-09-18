<?php
namespace blog\comments;

require_once __DIR__ . '/../domain/CommentRepo.php';
require_once __DIR__ . '/dto/CommentDTO.php';

class CommentController {

  private $repo;

  public function __construct(CommentRepo $repo){                                           
    $this->repo = $repo;
  }

  public function detailRequest($id) {
    $comment = $this->repo->fetchOne((int)$id);
    
    return new CommentDTO(
      $comment->id,
      $comment->body,
      $comment->timestamp,
      $comment->articleId
    );
  }
  
  public function listRequest($params) {
    $articleId = $this->getIfSet($params, 'articleId');   
    
    $comments = $this->repo->fetchAll((int)$articleId);
    
    $commentsDto = array();

    foreach ($comments as $comment) {
      $commentsDto[] = new CommentDTO(
        $comment->id,
        $comment->body,
        $comment->timestamp,
        $comment->articleId
      );
    }
    
    return $commentsDto;
  }
  
  private function getIfSet($params, $var, $def = null) {
    return isset($params[$var]) ? $params[$var] : $def;
  }
}