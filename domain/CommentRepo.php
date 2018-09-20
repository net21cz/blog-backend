<?php
namespace blog\comments;

interface CommentRepo {
 
    public function fetchAll($articleId, $limit);
    
    public function fetchOne($commentId);
    
    public function add($body, $articleId);
}