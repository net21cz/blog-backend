<?php
class Article {
 
    public $id;
    public $title;
    public $summary;
    public $text;
    
    public $categoryId;
    
    public $author;         
}

class ArticleAuthor {
  
    public $id;
    public $name;
    public $email;
}