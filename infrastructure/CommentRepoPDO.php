<?php
namespace blog\comments;

use \PDO;

require_once __DIR__ . '/../domain/Comment.php';
require_once __DIR__ . '/../domain/CommentRepo.php';

class CommentRepoPDO implements CommentRepo {
 
    private $conn;
    
    private $comments_table = "serendipity_comments";
  
    public function __construct(PDO $conn){
        $this->conn = $conn;
    }        
    
    public function fetchOne($commentId) {
        $q = "SELECT c.id, c.body, c.timestamp, c.entry_id articleId
                FROM {$this->comments_table} c
                WHERE c.id = :id ";
                        
        $stmt = $this->conn->prepare($q);        
        $stmt->bindValue('id', (int)$commentId, PDO::PARAM_INT);        
        $stmt->execute();
        
        $comment = null;  
               
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
          $comment = new Comment();
          
          $comment->id = (int)$row['id'];
          $comment->body = $row['body'];
          $comment->timestamp = $row['timestamp'];
          $comment->articleId = (int)$row['articleId'];
        }
             
        return $comment;
    }
    
    function fetchAll($articleId, $limit = 100) {   
        $q = "SELECT c.id, c.body, c.timestamp
                FROM {$this->comments_table} c
                WHERE c.entry_id = :articleId 
                ORDER BY c.timestamp DESC, c.id DESC
                LIMIT 0,:limit ";
                                              
        $stmt = $this->conn->prepare($q);        
        $stmt->bindValue('id', (int)$commentId, PDO::PARAM_INT);        
        $stmt->bindValue('limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $comments = array();  
               
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
          $comment = new Comment();
          
          $comment->id = (int)$row['id'];
          $comment->body = $row['body'];
          $comment->timestamp = $row['timestamp'];
          $comment->articleId = (int)$articleId;
          
          array_push($comments, $comment);
        }
             
        return $comments;
    }
}