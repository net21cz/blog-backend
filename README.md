# Blog Back-end Services

## Installation

Change credentials in `config/db.config.php`

## Usage

### Get a List of Articles
```
curl -X GET http://localhost/articles
curl -X GET http://localhost/articles?categoryId=123
curl -X GET http://localhost/articles?authorId=123
curl -X GET http://localhost/articles?categoryId=123&authorId=123
```

### Get a Detail of an Article
```
curl -X GET http://localhost/articles/123
```