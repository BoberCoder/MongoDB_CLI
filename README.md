MongoDB_CLI (Command Line Interface)
------------------------------------
[![Build Status](https://travis-ci.org/BoberCoder/MongoDB_CLI.svg?branch=master)](https://travis-ci.org/BoberCoder/MongoDB_CLI)

Install:

1. Clone app : `git clone https://github.com/BoberCoder/MongoDB_CLI.git`
 
2. Then go to the directory MongoDB_CLI and input: `composer install`
        
3. Done! Congratulations!!!

Usage:

Run application : `php mongocli.php`

`SELECT * FROM foo WHERE age >= 19 AND name = Vasya_Pupkin LIMIT 2 ORDER_BY surname ASC`

`SELECT name,surname FROM foo ORDER_BY surname ASC`

`SELECT name FROM foo WHERE age < 19 OR name = Mark SKIP 2 ORDER_BY city DESC`

-----

Tests:

To run tests input: `phpunit tests`

