# Headless middleware example

example code for my presentation on using a headless approach.

Feel free to use it, your API specific busineless logic goes into a model like Apicall/Httpbin/Get.php 

Please note there is a simple cache layer implemented in the controller you might want to tweak to your needs.

DI is used for several libraries that needed to be mocked in tests, configurable in dependencies.php

## Installation

Clone the project on your local machine.

- Make sure the log and cache directory are writable
- create a new database and import the transactions table schema
- Create `src/settings.php` from the template and add credentials

That's it

## Testing and code quality
Test the CSV POST endpoint using the postman collection provided.

This project uses GrumPHP for code quality running
- phpunit
- phpspec
- phpmd
- php-parse

to check all code in the project: `composer test`

Otherwise it'll act as a precommit hook