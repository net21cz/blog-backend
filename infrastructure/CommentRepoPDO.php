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
    
    public function fetchAll($articleId, $start = 0, $limit = 10) {   
        $q = "SELECT c.id, c.author, c.body, c.timestamp
                FROM {$this->comments_table} c
                WHERE c.entry_id = :articleId AND (c.parent_id = 0 OR c.parent_id IS NULL)
                ORDER BY c.timestamp DESC, c.id DESC
                LIMIT :start,:limit ";
                                      
        $stmt = $this->conn->prepare($q);        
        $stmt->bindValue('articleId', (int)$articleId, PDO::PARAM_INT);
        $stmt->bindValue('start', (int)$start, PDO::PARAM_INT);        
        $stmt->bindValue('limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $comments = array();  
               
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
          $comment = new Comment();
          
          $comment->id = (int)$row['id'];
          $comment->author = $row['author'];
          $comment->body = $row['body'];
          $comment->timestamp = $row['timestamp'];
          $comment->articleId = (int)$articleId;
          
          $comment->answers = $this->answers($comment->id, 0, $limit);
          
          array_push($comments, $comment);
        }
    
        return $comments;
    }
    
    public function answers($commentId, $start = 0, $limit = 10) {
        $q = "SELECT c.id, c.author, c.body, c.timestamp
              FROM {$this->comments_table} c
              WHERE c.parent_id = :id 
              ORDER BY c.timestamp ASC, c.id ASC
              LIMIT :start,:limit ";
                      
        $stmt = $this->conn->prepare($q);        
        $stmt->bindValue('id', (int)$commentId, PDO::PARAM_INT);
        $stmt->bindValue('start', (int)$start, PDO::PARAM_INT);
        $stmt->bindValue('limit', (int)$limit, PDO::PARAM_INT);        
        $stmt->execute();
        
        $answers = array();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
          $answer = new Comment();
        
          $answer->id = (int)$row['id'];
          $answer->author = $row['author'];
          $answer->body = $row['body'];
          $answer->timestamp = $row['timestamp'];
          
          $answers[] = $answer;
        }
        
        return $answers;
    }
    
    public function count($articleId, $commentId = null) {
        $q = "SELECT COUNT(DISTINCT c.id) count
                FROM {$this->comments_table} c ";
                
        $params = array();

        if ($articleId) {
            $q .= " WHERE c.entry_id = :articleId AND (c.parent_id = 0 OR c.parent_id IS NULL)";
            $params['articleId'] = (int)$articleId;
        }
        if ($commentId) {
            $q .= " WHERE c.parent_id = :commentId";
            $params['commentId'] = (int)$commentId;
        }
        
        $stmt = $this->conn->prepare($q);
        
        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value, PDO::PARAM_INT);
        }        
        $stmt->execute();
               
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)){           
          return (int)$row['count'];
        }             
        return 0;
    }
    
    public function add($author, $body, $articleId, $commentId = null) {
        $q = "INSERT INTO {$this->comments_table} (id, entry_id, timestamp, author, body, parent_id)
                VALUES (0, :articleId, :timestamp, :author, :body, :commentId)";
                        
        $stmt = $this->conn->prepare($q);               
        $stmt->bindValue('articleId', (int)$articleId, PDO::PARAM_INT);
        $stmt->bindValue('timestamp', time(), PDO::PARAM_INT);
        $stmt->bindValue('author', $author, PDO::PARAM_STR);
        $stmt->bindValue('body', $body, PDO::PARAM_STR);        
        $stmt->bindValue('commentId', (int)$commentId, PDO::PARAM_INT);
        $stmt->execute(); 
        
        $id = $this->conn->lastInsertId();  
               
        return $id;
    }
}