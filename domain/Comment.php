<?php
namespace blog\comments;

class Comment {
 
    public $id;       
    public $author;
    public $body;
    public $timestamp;
    
    public $answers;
    
    public $articleId;      
}

class Answer {
  
    public $id;    
    public $author;
    public $body;
    public $timestamp;
}