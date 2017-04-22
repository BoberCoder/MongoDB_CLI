<?php

require_once __DIR__.'/../../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use MongoDB_CLI\Transformer;
use MongoDB\Client as MongoClient;

class TransformerTest extends TestCase
{
    private $transformer;

    public function setUp()
    {
        $mongo = new MongoClient('mongodb://127.0.0.1/test', array('username'=>"myTester",'password'=>"xyz123"));
        $collection = $mongo->test->foo;

        $this->transformer = new Transformer($collection);
    }

    public function testgetAll()
    {
        $actual = $this->transformer->getAll();

        $this->assertEquals([],$actual);

    }

    public function testGetProjections()
    {
        $projections = ["name","surname","age"];

        $actual = $this->transformer->getProjections($projections);

        $this->assertEquals(["name"=>1,"surname"=>1,"age"=>1],$actual);
    }

    public function testExpressionGenerator()
    {
        $sql = ["SELECT","*","FROM","foo","WHERE","name","=","Eughene","AND","age",">=","19"];

        $word = array_keys($sql,"WHERE");
        $word = array_merge($word,array_keys($sql,"AND"));

        $actual = $this->transformer->expressionGenerator($sql,$word);
        $expected = [["property" => "name","condition" => "=","value" => "Eughene"],
        ["property" => "age","condition" => ">=","value" => "19"]];

        $this->assertEquals($expected,$actual);

    }
}