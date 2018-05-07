<?php
namespace blog\articles;

class ArticleDetailDTO {

  public $id;
  public $title;
  public $summary;
  public $body;
  public $createdAt;
  public $categoryId;
  public $author;

  public function __construct($id, $title, $summary, $body, $createdAt, $categoryId, $author) {
    $this->id = $id;
    $this->title = $title;
    $this->summary = $summary;                                           
    $this->body = $body;
    $this->createdAt = $createdAt;
    $this->categoryId = $categoryId;
    $this->author = $author;
  }
}