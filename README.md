# Blog Back-end Services

## Installation

Change credentials in `config/db.config.php`

## Usage

### Get a List of Articles
```
curl -X GET articles.php
curl -X GET articles.php?categoryId=123
curl -X GET articles.php?authorId=123
curl -X GET articles.php?categoryId=123&authorId=123
```

### Get a Detail of an Article
```
curl -X GET articles.php?id=123
```