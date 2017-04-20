<?php

namespace MongoDB_CLI;

class Transformer
{
    private $collection;

    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    public function getAll()
    {
        return [];
    }

    public function getProgections($projections)
    {
        $array = [];

        for ($i=0;$i<count($projections);$i++)
        {
            $array[$projections[$i]] = 1;
        }

        return $array;

    }

    public function whereCondition($expressions,$operation = "")
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

        if ($operation == "OR")
        {
            $oroptions = [];
            foreach ($options as $key => $value)
            {
                $oroptions['$or'][] = [$key => $value];
            }

            return $oroptions;
        }
        else
        {
            return $options;
        }

    }

    public function expressionGenerator($sql,$word)
    {
        $expression = [];

        for($i=0;$i<count($word);$i++)
        {
            $expression[] =
                [
                    "property" => $sql[$word[$i] + 1],
                    "condition" => $sql[$word[$i] + 2],
                    "value" => $sql[$word[$i] + 3]
                ];
        }

        return $expression;
    }


    public function executeQuery($options,$projections,$limit,$skip)
    {
        if ($limit and $skip)
        {
            echo 1;
            $results = $this->collection->find($options, ['projection'=> $projections, 'skip' => $skip ,'limit' => $limit]);
        }
        elseif ($skip)
        {
            $results = $this->collection->find($options, ['projection'=> $projections, 'skip' => $skip]);
        }
        elseif ($limit)
        {
            echo 2;
            $results = $this->collection->find($options, ['projection'=> $projections, 'limit' => $limit]);
        }
        else
        {
            $results = $this->collection->find($options, ['projection'=> $projections]);
        }

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