# Hacker News API Laravel

## Installation
- `` cd /hacker-news-api``
- `` composer install``

## Usage
 1. **Top 10 most occurring words in the titles of the last 25 stories**
- API: ``/api/last-25`` 
- Response Format: ``{
status: "success", 
words: []
}
``

2. **Top 10 most occurring words in the titles of the post of exactly the last week**
- API: ``/api/last-week``
- Response Format: ``{
  status: "success",
  words: []
  }
  ``
3. **Top 10 most occurring words in titles of the last 600 stories of users with at least 10.000 karma **
- API: ``/api/last-600``
- Response Format: ``{
  status: "success",
  words: []
  }
  ``

