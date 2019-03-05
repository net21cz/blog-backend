<?php
namespace blog\comments;

require_once __DIR__ . '/../domain/CommentRepo.php';
require_once __DIR__ . '/dto/CommentDTO.php';

class CommentController {

  private $repo;

  public function __construct(CommentRepo $repo){                                           
    $this->repo = $repo;
  }
  
  private $limit = 3;
  
  public function listRequest($articleId, $page = 0) {
    $comments = $this->repo->fetchAll((int)$articleId, $page * $this->limit, $this->limit);
    
    $commentsDto = array();

    foreach ($comments as $comment) {
      $answersDto = array();

      foreach ($comment->answers as $answer) {
        $answersDto[] = new AnswerDTO(
          $answer->id,
          $answer->author,
          $answer->body,
          $answer->timestamp
        );
      }
      
      $count = $this->repo->count(null, $comment->id);
      
      $commentsDto[] = new CommentDTO(
        $comment->id,
        $comment->author,
        $comment->body,
        $comment->timestamp,
        $answersDto,
        $this->limit < $count ? 1 : null
      );
    }
    
    $count = $this->repo->count((int)$articleId);
    
    return array('comments' => $commentsDto, 'next' => (($page + 1) * $this->limit) < $count ? $page + 1 : null);
  }
  
  public function answersRequest($commentId, $page = 0) {
    $answers = $this->repo->answers((int)$commentId, $page * $this->limit, $this->limit);
    
    $answersDto = array();

    foreach ($answers as $answer) {
          
      $answersDto[] = new AnswerDTO(
        $answer->id,
        $answer->author,
        $answer->body,
        $answer->timestamp
      );
    }
    
    $count = $this->repo->count(null, $commentId);
    
    return array('answers' => $answersDto, 'next' => (($page + 1) * $this->limit) < $count ? $page + 1 : null);
  }

  public function addRequest($params, $articleId, $commentId = null) {
    $author = $params['author'];
    $body = $params['body'];
    
    $id = $this->repo->add($author, $body, (int)$articleId, (int)$commentId);
    
    return new BaseCommentDTO($id, $author, $body, time());
  }
  
  private function getIfSet($params, $var, $def = null) {
    return isset($params[$var]) ? $params[$var] : $def;
  }
}