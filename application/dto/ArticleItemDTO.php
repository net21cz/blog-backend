<?php
namespace blog\articles;

class ArticleItemDTO {

  public $id;
  public $title;
  public $summary;
  public $createdAt;
  public $categoryId;
  public $author;

  public function __construct($id, $title, $summary, $createdAt, $categoryId, $author) {
    $this->id = $id;
    $this->title = $title;
    $this->summary = $summary;    
    $this->createdAt = $createdAt;
    $this->categoryId = $categoryId;
    $this->author = $author;
  }
}