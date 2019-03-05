<?php
namespace blog\comments;

class BaseCommentDTO {

  public $id;
  public $author;
  public $body;
  public $createdAt;

  public function __construct($id, $author, $body, $createdAt) {
    $this->id = $id;    
    $this->author = $author;                                       
    $this->body = $body;
    $this->createdAt = $createdAt;
  }
}

class CommentDTO extends BaseCommentDTO {
  
  public $answers;
  public $next;

  public function __construct($id, $author, $body, $createdAt, $answers, $next) {
    parent::__construct($id, $author, $body, $createdAt);
    
    $this->answers = $answers;
    $this->next = $next;
  }
}

class AnswerDTO extends BaseCommentDTO {

  public function __construct($id, $author, $body, $createdAt) {
    parent::__construct($id, $author, $body, $createdAt);
  }
}