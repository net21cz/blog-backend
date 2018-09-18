<?php
namespace blog\comments;

class CommentDTO {

  public $id;
  public $body;
  public $createdAt;
  public $articleId;

  public function __construct($id, $body, $createdAt, $articleId) {
    $this->id = $id;                                           
    $this->body = $body;
    $this->createdAt = $createdAt;
    $this->articleId = $articleId;
  }
}