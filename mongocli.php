#!php
<?php

require 'vendor/autoload.php';

use MongoDB\Client as MongoClient;

echo "\033[36mWelcome to MongoDB CLI. Available databases:\033[0m \n";

$admin = new MongoClient('mongodb://127.0.0.1/admin', array('username'=>'Admin','password'=>'1312346456bob'));

$dbs = $admin->listDatabases();
foreach ($dbs as $db){
    echo $db->getName(). "\n";
}


echo "\033[36mPlease enter database which you want to use: \033[0m";
$database = trim(fgets(STDIN));
echo "\033[36mEnter your username: \033[0m";
$username = trim(fgets(STDIN));
echo "\033[36mAnd password: \033[0m";
$password = trim(fgets(STDIN));

$m = new MongoClient('mongodb://127.0.0.1/'.$database, array('username'=>$username,'password'=>$password));


start:
echo "\033[36mEnter your query: \033[0m";
$query = trim(fgets(STDIN));

$pieces =  preg_split("/[\s,]+/", $query);


if ($pieces[0] == 'SELECT'){
    if ($pieces[1] == "*"){
        $from = array_search("FROM",$pieces);
        $col = $pieces[$from + 1];
        $collection = $m->$database->$col;
        $records = $collection->find();
        foreach ($records as $record) {
            foreach ($record as $key => $value){
                echo '|' . $record[$key] . '|  ';
            }
            echo "\n";
        }
    }
    else{
        $from = array_search("FROM",$pieces);
        $projections = array_slice($pieces,1,$from-1);
        $col = $pieces[$from + 1];
        $collection = $m->$database->$col;
        $records = $collection->find();
        $n = count($projections);
        foreach ($records as $record) {
            for ($i = 0; $i < $n; $i++) {
                echo '|' . $record[$projections[$i]] . '|  ';
            }
            echo "\n";
        }
    }

    decision:
    echo "\033[36mContinue use MongoDB_CLI (\"y/n\")? \033[0m";
    $decision =  trim(fgets(STDIN));
    if ($decision == 'y'){
        goto start;
    }
    elseif ($decision == 'n'){
        
        die('bye'."\xA");
    }
    else{
        echo "\xA";
        goto decision;
    }


}
