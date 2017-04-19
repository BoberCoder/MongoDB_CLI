<?php

namespace MongoDB_CLI;

class Transformer
{
    private $collection;

    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    public function getAllResult()
    {
        $result = $this->collection->find();

        return $result;


    }

    public function getResultByProgections($projections)
    {
        for ($i=0;$i<count($projections);$i++)
        {
            $array[$projections[$i]] = 1;
        }

        $results = $this->collection->find([], ['projection'=> $array]);

        return $results;

    }

    public function whereCondition($expressions)
    {
        $options = [];

        for($i=0;$i<count($expressions);$i++)
        {
            $expression = $expressions[$i];

            if ((int)$expression["value"])
            {
                $expression["value"] = (int)$expression["value"];
            }

            switch ($expression["condition"])
            {
                case "<":
                   $options[$expression["property"]] = array('$lt' => $expression["value"]);
                   break;
                case "<=":
                    $options[$expression["property"]] =  array('$lte' => $expression["value"]);
                    break;
                case ">":
                    $options[$expression["property"]] =  array('$gt' => $expression["value"]);
                    break;
                case ">=":
                    $options[$expression["property"]] = array('$gte' => $expression["value"]);
                    break;
                case "=":
                    $options[$expression["property"]] =  $expression["value"];
                    break;
                case "<>":
                    $options[$expression["property"]] = array('$ne' => $expression["value"]);
                    break;
            }

        }

        var_dump($options);
        $results = $this->collection->find($options);
        return $results;

    }

    public function echoResult($results)
    {
        foreach ($results as $result) {
            foreach ($result as $key => $value) {
                echo '|' . $result[$key] . '|  ';
            }
            echo "\n";
        }
    }
}