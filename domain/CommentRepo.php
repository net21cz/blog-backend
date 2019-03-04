<?php
namespace blog\comments;

interface CommentRepo {
 
    public function fetchAll($articleId, $limit);
    
    public function count($articleId, $commentId);
    
    public function answers($commentId, $start, $limit);
    
    public function add($author, $body, $articleId, $commentId);
}