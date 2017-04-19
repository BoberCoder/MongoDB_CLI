#!php
<?php

include 'vendor/autoload.php';

use MongoDB\Client as MongoClient;
use MongoDB_CLI\Transformer;

echo "\033[36mWelcome to MongoDB CLI. Available databases:\033[0m \n";


/**
 * Display all available databases
 */
//$admin = new MongoClient('mongodb://127.0.0.1/admin', array('username'=>'Admin','password'=>'1312346456bob'));
//
//$dbs = $admin->listDatabases();
//foreach ($dbs as $db){
//    echo $db->getName(). "\n";
//}


/**
 * Connect to MongoDB as logged user
 */
//echo "\033[36mPlease enter database which you want to use: \033[0m";
//$database = trim(fgets(STDIN));
//echo "\033[36mEnter your username: \033[0m";
//$username = trim(fgets(STDIN));
//echo "\033[36mAnd password: \033[0m";
//$password = trim(fgets(STDIN));

$mongo = new MongoClient('mongodb://127.0.0.1/test', array('username'=>"myTester",'password'=>"xyz123"));


/**
 * Query input and division in array
 */
start:
echo "\033[36mEnter your query: \033[0m";
$query = trim(fgets(STDIN));
$sql =  preg_split("/[\s,]+/", $query);


/**
 * Executing SQL
 */
if ($sql[0] == "SELECT") {

    $collection = $sql[array_search("FROM",$sql) + 1];
    $collection = $mongo->test->$collection;


    $transformer = new Transformer($collection);


    if ($sql[1] == "*")
    {
        if($where = array_search("WHERE",$sql))
        {
            $expression = [];
            $expression[0] = [
                    "property" => $sql[$where + 1],
                    "condition" => $sql[$where + 2],
                    "value" => $sql[$where + 3]
            ];

            if ($and = array_keys($sql,"AND"))
            {
                for($i=0;$i<count($and);$i++)
                {
                    $expression[] =
                        [
                            "property" => $sql[$and[$i] + 1],
                            "condition" => $sql[$and[$i] + 2],
                            "value" => $sql[$and[$i] + 3]
                        ];
                }
            }
            $results = $transformer->whereCondition($expression);
        }
        else
        {
            $results = $transformer->getAllResult();
        }
    }
    else
    {
        $projections = array_slice($sql, 1, array_search("FROM",$sql) - 1);
        $results = $transformer->getResultByProgections($projections);
    }

    $transformer->echoResult($results);
}
else
{
    echo "\033[31mIncorrect method or this method does't allow. Available methods: \033[0mSELECT \n";
}



/**
 * Exit proposal
 */
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

