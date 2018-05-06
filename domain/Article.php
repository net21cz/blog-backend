<?php
namespace blog\articles;

class Article {
 
    public $id;
    public $title;
    public $summary;
    public $text;
    public $timestamp;
    
    public $categoryId;
    
    public $author;         
}

class ArticleAuthor {
  
    public $id;
    public $name;
    public $email;
}