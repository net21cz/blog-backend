<?php
namespace blog;

class BlogInfoDTO {

  public $title;
  public $description;  
  public $categories;

  public function __construct($title, $description, $categories) {
    $this->title = $title;                                           
    $this->description = $description;
    $this->categories = $categories;
  }
}

class CategoryDTO {
  
  public $id;
  public $name;
  
  public function __construct($id, $name) {
    $this->id = $id;
    $this->name = $name;
  }
}