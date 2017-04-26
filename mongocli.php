#!php
<?php

include 'vendor/autoload.php';

use MongoDB\Client as MongoClient;
use MongoDB_CLI\Transformer;


/**
 * Get config from file
 */
$config = json_decode(file_get_contents("config/config.json"));


echo "\033[36mWelcome to MongoDB CLI. Available databases:\033[0m \n";

/**
 * Display all available databases
 */
$admin = new MongoClient('mongodb://127.0.0.1/admin');

$dbs = $admin->listDatabases();
foreach ($dbs as $db){
    echo $db->getName(). "\n";
}



/**
 * Connect to MongoDB as logged user
 */
echo "\033[36mPlease enter database which you want to use: \033[0m";
$database = trim(fgets(STDIN));

$mongo = new MongoClient("mongodb://127.0.0.1/".$database);




/**
 * Display all available collections in selected database
 */
echo "\n\033[36mAvailable collections in this database:\033[0m \n";

$collections = $mongo->$database->listCollections();
foreach ($collections as $collection){
    echo $collection->getName(). "\n";
}



/**
 * Query input and division in array
 */
start:
echo "\n\033[36mEnter your query: \033[0m";
$query = trim(fgets(STDIN));
$sql =  preg_split("/[\s,]+/", $query);


/**
 * Executing SQL
 */
if ($sql[0] == "SELECT") {


    /**
     * Collection determination
     */
    if (array_search("FROM",$sql))
    {
        $collection = $sql[array_search("FROM",$sql) + 1];
        $collection = $mongo->$database->$collection;
    }
    else
    {
        echo "\033[31mExpression 'FROM' is lost\033[0m\n";
        goto start;
    }


    $transformer = new Transformer($collection);

    $options = [];
    $projections = [];
    $operation = "";


    if ($sql[1] == "*")
    {

        $projections = $transformer->getAll();

        if($where = array_keys($sql,"WHERE"))
        {
            $expression = $transformer->expressionGenerator($sql,$where);

            if ($and = array_keys($sql,"AND"))
            {
                $expression = array_merge($expression,$transformer->expressionGenerator($sql,$and));
                $operation = "AND";
            }
            elseif ($or = array_keys($sql,"OR"))
            {
                $expression = array_merge($expression,$transformer->expressionGenerator($sql,$or));
                $operation = "OR";
            }

            $options = $transformer->whereCondition($expression,$operation);
        }
    }
    else
    {
        $projections = array_slice($sql, 1, array_search("FROM",$sql) - 1);
        $projections = $transformer->getProjections($projections);

        if($where = array_keys($sql,"WHERE"))
        {
            $expression = $transformer->expressionGenerator($sql,$where);

            if ($and = array_keys($sql,"AND"))
            {
                $expression = array_merge($expression,$transformer->expressionGenerator($sql,$and));
                $operation = "AND";
            }
            elseif ($or = array_keys($sql,"OR"))
            {
                $expression = array_merge($expression,$transformer->expressionGenerator($sql,$or));
                $operation = "OR";
            }

            $options = $transformer->whereCondition($expression,$operation);
        }
    }

    /**
     * Limit value define
     */
    $limit = [];
    if(array_search("LIMIT",$sql))
    {
       $limit = $transformer->getLimitValue($sql);
    }

    /**
     * Skip value define
     */
    $skip = [];
    if(array_search("SKIP",$sql))
    {
        $skip = $transformer->getSkipValue($sql);
    }

    /**
     * Order By value define
     */
    if(array_search("ORDER_BY",$sql))
    {
        $order = $transformer->getOrderByValue($sql);
    }
    else
    {
        $order = ["property" => "_id", "val" => 1];
    }


    $results = $transformer->executeQuery($options,$projections,$limit,$skip,$order);
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

