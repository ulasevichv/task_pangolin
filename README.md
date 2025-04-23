## Requirements:

- PHP version: ^8.2
- Laravel version: ^12.0

## Usage information:

- API request:
  - /api/v1/best-sellers/search `[POST]`
  - Payload:
    ```js
    {
        "author": "",
        "title": "",
        "isbn": [
        ],
        "offset": 0
    }
    ```
- Test command: `php artisan test --testsuite=Feature`
- .env-file parameters:
  - `NEW_YORK_TIMES_SERVICE_API_KEY` 

## Notes on implementation:

- Task is implemented on a fresh Laravel 12 installation
- Tests will execute without a valid NYT API credentials and internet connection
- Multiple ISBNs are not working in NYT API itself - check the following link:
  <br/>&nbsp;&nbsp;&nbsp;&nbsp;https://api.nytimes.com/svc/books/v3/lists/best-sellers/history.json?api-key=P4I3JPvAuX6vA7TEeDMYG8NBb4DylKhA&isbn=1451627289;1476713340
<br/>&nbsp;&nbsp;&nbsp;&nbsp;or test manually here: https://developer.nytimes.com/docs/books-product/1/routes/lists/best-sellers/history.json/get