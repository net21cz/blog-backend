<?php
namespace blog;

class BlogInfoDTO {

  private $title;
  private $description;
  
  private $categories;

  public function __construct($title, $description, $categories) {
    $this->title = $title;                                           
    $this->description = $description;
    $this->categories = $categories;
  }
  
  public function title() {
    return $this->title;
  }
  
  public function description() {
    return $this->description;
  }
  
  public function categories() {
    return $this->categories;
  }
}

class CategoryDTO {
  
  private $id;
  private $name;
  
  public function __construct($id, $name) {
    $this->id = $id;
    $this->name = $name;
  }
  
  public function id() {
    return $this->id;
  }
  
  public function name() {
    return $this->name;
  }
}