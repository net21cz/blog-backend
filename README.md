# Blog Back-end Services

## Installation

Change credentials in `config/db.config.php`

## Usage

### Get a List of Articles
```
curl http://localhost/articles
curl http://localhost/articles?categoryId=123
curl http://localhost/articles?authorId=123
curl http://localhost/articles?categoryId=123&authorId=123
```

### Get a Detail of an Article
```
curl http://localhost/articles/123
```