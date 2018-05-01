<?php
include_once 'model/Article.php';
include_once 'model/ArticleRepo.php';

class ArticleRepoImpl implements ArticleRepo {
 
    private $conn;
    
    private $articles_table = "serendipity_entries";
    private $articles_categories_table = "serendipity_entrycat";
    private $authors_table = "serendipity_authors";
  
    public function __construct($conn){
        $this->conn = $conn;
    }
    
    function fetchAll($categoryId = null, $authorId = null, $start = 0, $limit = 10) {   
        $q = "SELECT a.id, a.title, a.body summary, ac.categoryId, au.authorId, au.realname authorName, au.email authorEmail
                FROM " . $this->articles_table . " a
                    LEFT JOIN " . $this->articles_categories_table . " ac ON a.id = ac.entryid
                    LEFT JOIN " . $this->authors_table . " au ON a.authorid = au.authorid
                WHERE 1=1 ";
                
        $params = array('start' => (int)$start, 'limit' => (int)$limit);

        if ($categoryId) {
            $q .= " AND ac.categoryid = :categoryId";
            $params['categoryId'] = (int)$categoryId;
        }
        if ($authorId) {
            $q .= " AND au.authorId = :authorId";
            $params['authorId'] = (int)$authorId;
        }                    
                    
        $q .="  ORDER BY a.timestamp DESC, a.id DESC
                LIMIT :start,:limit";
        
        $stmt = $this->conn->prepare($q);
        
        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        
        $articles = array();  
               
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
          $article = new Article();
          
          $article->id = (int)$row['id'];
          $article->title = $row['title'];
          $article->summary = $row['summary'];
          $article->text = '';
          $article->categoryId = (int)$row['categoryId'];
          
          $article->author = new ArticleAuthor();
          $article->author->id = (int)$row['authorId'];
          $article->author->name = $row['authorName'];
          $article->author->email = $row['authorEmail'];
          
          array_push($articles, $article);
        }
             
        return $articles;
    }
    
    public function fetchOne($articleId) {
        $q = "SELECT a.id, a.title, a.body summary, a.extended text, ac.categoryId, au.authorId, au.realname authorName, au.email authorEmail
                FROM " . $this->articles_table . " a
                    LEFT JOIN " . $this->articles_categories_table . " ac ON a.id = ac.entryid
                    LEFT JOIN " . $this->authors_table . " au ON a.authorid = au.authorid
                WHERE a.id = :id ";
                        
        $stmt = $this->conn->prepare($q);        
        $stmt->bindValue('id', (int)$articleId, PDO::PARAM_INT);        
        $stmt->execute();
        
        $article = null;  
               
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
          $article = new Article();
          
          $article->id = (int)$row['id'];
          $article->title = $row['title'];
          $article->summary = $row['summary'];
          $article->text = $row['text'];
          $article->categoryId = (int)$row['categoryId'];
          
          $article->author = new ArticleAuthor();
          $article->author->id = (int)$row['authorId'];
          $article->author->name = $row['authorName'];
          $article->author->email = $row['authorEmail'];
        }
             
        return $article;
    }
}