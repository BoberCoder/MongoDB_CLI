MongoDB_CLI (Command Line Interface)
------------------------------------
Install:

1. Clone app : `git clone https://github.com/BoberCoder/MongoDB_CLI.git`
 
2. Then go to the directory MongoDB_CLI and input: `composer install`

3. Edit config.php with any text editor (for example "gedit"): `gedit config.php`, and rewrite your admin user credentials for appropriate fields:
    
        $config["database"] = "admin";
        $config["username"] = "Admin";
        $config["password"] = "admin";   
4. Done! Congratulations!!!

Usage:

`SELECT * FROM foo WHERE age >= 19 AND name = Vasya_Pupkin LIMIT 2 ORDER_BY surname ASC`

`SELECT name,surname FROM foo ORDER_BY surname ASC`

`SELECT name FROM foo WHERE age < 19 OR name = Mark SKIP 2 ORDER_BY city DESC`