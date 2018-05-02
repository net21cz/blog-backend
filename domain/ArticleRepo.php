<?php
namespace articles;

interface ArticleRepo {
 
    public function fetchAll($categoryId, $authorId, $start, $limit);
    
    public function fetchOne($articleId);
}