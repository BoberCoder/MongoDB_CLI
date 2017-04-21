<?php

require_once '/var/www/MongoDB_CLI/vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use MongoDB_CLI\Transformer;
use MongoDB\Client as MongoClient;

class TransformerTest extends TestCase
{

    function testgetAllFields()
    {
        $mongo = new MongoClient('mongodb://127.0.0.1/test', array('username'=>"myTester",'password'=>"xyz123"));
        $collection = $mongo->test->foo;
        $transformer = new Transformer($collection);

        $projections = $transformer->getAll();
        $expected = $transformer->executeQuery([],$projections,[],[],$order = ["property" => "_id", "val" => 1]);

        $actual = $collection->find([],[]);

        $this->assertEquals($expected,$actual);

    }
}