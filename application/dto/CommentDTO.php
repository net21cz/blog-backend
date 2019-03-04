<?php
namespace blog\comments;

class CommentDTO {

  public $id;
  public $author;
  public $body;
  public $createdAt;
  
  public $answers;
  public $next;

  public function __construct($id, $author, $body, $createdAt, $answers, $next) {
    $this->id = $id;     
    $this->author = $author;                                      
    $this->body = $body;
    $this->createdAt = $createdAt;
    $this->answers = $answers;
    $this->next = $next;
  }
}

class AnswerDTO {

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